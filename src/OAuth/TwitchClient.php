<?php

declare(strict_types=1);

namespace App\OAuth;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwitchClient implements ClientInterface
{
    use ClientRefreshTokenTrait;

    private string $redirectUri;
    /**
     * @var array<array-key, string>
     */
    private array $scopes = [];

    private string $clientSecret;

    private string $clientId;

    /**
     * @var array{created: int, access_token: string, expires_in: int, refresh_token: string}|null
     */
    private ?array $token = null;

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public static function getName(): string
    {
        return 'twitch';
    }

    public static function getSessionKey(): string
    {
        return 'twitch_access_token';
    }

    public function initRedirectUri(string $host, UrlGeneratorInterface $urlGenerator): void
    {
        $this->setRedirectUri(
            sprintf(
                '%s%s',
                $host,
                $urlGenerator->generate('twitch_check')
            )
        );
    }

    public function setRedirectUri($redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    public function setScopes($scopes): void
    {
        $this->scopes = $scopes;
    }

    public function createAuthUrl(): string
    {
        return sprintf(
            'https://id.twitch.tv/oauth2/authorize?client_id=%s&redirect_uri=%s&response_type=code&scope=%s',
            $this->clientId,
            $this->redirectUri,
            implode('+', $this->scopes)
        );
    }

    public function setAccessToken($accessToken): void
    {
        $this->token = $accessToken;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function isAccessTokenExpired(): bool
    {
        if (null === $this->token) {
            return true;
        }

        return $this->token['created'] + $this->token['expires_in'] - 30 < time();
    }

    /**
     * @return array{created?: int, access_token?: string, expires_in?: int, refresh_token?: string}
     */
    public function fetchAccessTokenWithAuthCode($code): array
    {
        $response = $this->httpClient->request('POST', 'https://id.twitch.tv/oauth2/token', [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
            ],
        ]);

        return $response->toArray();
    }

    /**
     * @return array{created?: int, access_token?: string, expires_in?: int, refresh_token?: string}
     */
    public function fetchAccessTokenWithRefreshToken($refreshToken): array
    {
        $response = $this->httpClient->request('POST', 'https://id.twitch.tv/oauth2/token', [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ],
        ]);

        return $response->toArray();
    }

    public function getAccessToken()
    {
        return $this->token;
    }
}
