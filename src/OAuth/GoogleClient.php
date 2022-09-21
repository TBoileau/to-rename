<?php

declare(strict_types=1);

namespace App\OAuth;

use Google\Client;

final class GoogleClient extends Client implements ClientInterface
{
    public static function getName(): string
    {
        return 'google';
    }

    public static function getSessionKey(): string
    {
        return 'google_access_token';
    }
}
