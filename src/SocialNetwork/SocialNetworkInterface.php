<?php

declare(strict_types=1);

namespace App\SocialNetwork;

interface SocialNetworkInterface
{
    public function send(string $message, ?string $image = null): void;
}
