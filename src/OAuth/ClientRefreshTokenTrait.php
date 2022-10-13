<?php

declare(strict_types=1);

namespace App\OAuth;

use Symfony\Contracts\Service\Attribute\Required;

trait ClientRefreshTokenTrait
{
    private RefreshTokenInterface $refreshToken;

    #[Required]
    public function setRefreshToken(RefreshTokenInterface $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function refresh(): void
    {
        $this->refreshToken->refresh($this);
    }
}
