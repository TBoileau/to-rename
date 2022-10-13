<?php

declare(strict_types=1);

namespace App\OAuth;

interface RefreshTokenInterface
{
    public function refresh(ClientInterface $client): void;
}
