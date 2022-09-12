<?php

declare(strict_types=1);

namespace App\OAuth\Security\Token;

use App\OAuth\ClientInterface;

final class OAuthToken implements TokenInterface
{
    public function __construct(private ClientInterface $client)
    {
    }

    public function save(array $accessToken): void
    {
        $this->client->setAccessToken($accessToken);
    }

    public function isAuthenticated(): bool
    {
        return !$this->client->isAccessTokenExpired();
    }
}
