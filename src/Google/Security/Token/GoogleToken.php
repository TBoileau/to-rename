<?php

declare(strict_types=1);

namespace App\Google\Security\Token;

use Google_Client;

final class GoogleToken implements TokenInterface
{
    public function __construct(private Google_Client $googleClient)
    {
    }

    public function save(array $accessToken): void
    {
        $this->googleClient->setAccessToken($accessToken);
    }

    public function isAuthenticated(): bool
    {
        return !$this->googleClient->isAccessTokenExpired();
    }
}
