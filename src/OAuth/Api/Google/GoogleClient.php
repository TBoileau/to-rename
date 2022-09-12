<?php

declare(strict_types=1);

namespace App\OAuth\Api\Google;

use App\OAuth\ClientInterface;
use App\OAuth\ClientTrait;
use Google\Client;

final class GoogleClient extends Client implements ClientInterface
{
    use ClientTrait;

    public static function getName(): string
    {
        return 'google';
    }
}
