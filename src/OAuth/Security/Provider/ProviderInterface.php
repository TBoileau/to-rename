<?php

declare(strict_types=1);

namespace App\OAuth\Security\Provider;

use Symfony\Component\HttpFoundation\Request;

interface ProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function fetchAccessToken(Request $request): array;

    /**
     * @return array<string, mixed>
     */
    public function fetchAccessTokenWithRefreshToken(string $refreshToken): array;
}
