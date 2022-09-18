<?php

declare(strict_types=1);

namespace App\OAuth\Api\LinkedIn;

use App\OAuth\ClientInterface;
use App\OAuth\ClientTrait;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class LinkedInClient implements ClientInterface
{
    use ClientTrait;

    private string $redirectUri;

    private string $scopes;

    private string $clientId;

    private string $clientSecret;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $accessToken = null;

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @param string $accessType
     *
     * @return void
     */
    public function setAccessType($accessType)
    {
    }

    public static function getName(): string
    {
        return 'linkedin';
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string $url
     */
    public function setRedirectUri($url): void
    {
        $this->redirectUri = $url;
    }

    /**
     * @param string|array<array-key, string> $scopes
     */
    public function setScopes($scopes): void
    {
        if (is_array($scopes)) {
            $this->scopes = implode(' ', $scopes);
        } else {
            $this->scopes = $scopes;
        }
    }

    public function createAuthUrl(): string
    {
        return sprintf(
            'https://www.linkedin.com/oauth/v2/authorization?%s',
            http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => $this->scopes,
            ])
        );
    }

    /**
     * @param string|array<string, mixed> $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        if (null == $accessToken) {
            throw new InvalidArgumentException('invalid json token');
        }

        if (!isset($accessToken['access_token'])) {
            throw new InvalidArgumentException('Invalid token format');
        }

        $this->accessToken = $accessToken;
    }

    public function isAccessTokenExpired(): bool
    {
        if (null === $this->accessToken) {
            return true;
        }

        $created = $this->accessToken['created'];

        return ($created + ($this->accessToken['expires_in'] - 30)) < time();
    }

    public function fetchAccessTokenWithAuthCode($code): array
    {
        $response = $this->httpClient->request(Request::METHOD_POST, 'https://www.linkedin.com/oauth/v2/accessToken', [
            'body' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
            ],
        ]);

        return $response->toArray();
    }

    public function tweet(string $message): void
    {
        $this->httpClient->request(Request::METHOD_POST, 'https://api.twitter.com/2/tweets', [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->accessToken['access_token']), /* @phpstan-ignore-line */
            ],
            'json' => [
                'text' => $message,
            ],
        ]);
    }

    public function fetchAccessTokenWithRefreshToken($refreshToken)
    {
        return [];
    }

    public function getProfileId(): string
    {
        $response = $this->httpClient->request(Request::METHOD_GET, 'https://api.linkedin.com/v2/me?projection=(id)', [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->accessToken['access_token']), /* @phpstan-ignore-line */
            ],
        ]);

        return $response->toArray()['id'];
    }
}
