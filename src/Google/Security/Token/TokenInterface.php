<?php

declare(strict_types=1);

namespace App\Google\Security\Token;

interface TokenInterface
{
    /**
     * @param array<string, mixed> $accessToken
     */
    public function save(array $accessToken): void;

    public function isAuthenticated(): bool;
}
