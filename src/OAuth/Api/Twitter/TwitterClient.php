<?php

declare(strict_types=1);

namespace App\OAuth\Api\Twitter;

use App\OAuth\ClientInterface;
use App\OAuth\ClientTrait;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwitterClient implements ClientInterface
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

    public static function getName(): string
    {
        return 'twitter';
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
            'https://twitter.com/i/oauth2/authorize?%s',
            http_build_query([
                'response_type' => 'code',
                'code_challenge' => 'challenge',
                'code_challenge_method' => 'plain',
                'state' => 'state',
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

        $accessToken['created'] = time();

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
        $response = $this->httpClient->request(Request::METHOD_POST, 'https://api.twitter.com/2/oauth2/token', [
            'headers' => [
                'Authorization' => sprintf('Basic %s', base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret))),
            ],
            'body' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'code_verifier' => 'challenge',
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
}
