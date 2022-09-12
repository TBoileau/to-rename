<?php

declare(strict_types=1);

namespace App\OAuth\Security\Token;

interface TokenInterface
{
    /**
     * @param array<string, mixed> $accessToken
     */
    public function save(array $accessToken): void;

    public function isAuthenticated(): bool;
}
