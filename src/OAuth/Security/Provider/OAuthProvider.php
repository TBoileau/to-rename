<?php

declare(strict_types=1);

namespace App\OAuth\Security\Provider;

use App\OAuth\ClientInterface;
use Symfony\Component\HttpFoundation\Request;

final class OAuthProvider implements ProviderInterface
{
    public function __construct(private ClientInterface $client)
    {
    }

    public function fetchAccessToken(Request $request): array
    {
        /* @phpstan-ignore-next-line */
        return $this->client->fetchAccessTokenWithAuthCode($request->get('code'));
    }

    public function fetchAccessTokenWithRefreshToken(string $refreshToken): array
    {
        return $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
    }
}
