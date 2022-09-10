<?php

declare(strict_types=1);

namespace App\Google\Security\Provider;

use Google_Client;
use Symfony\Component\HttpFoundation\Request;

final class GoogleProvider implements ProviderInterface
{
    public function __construct(private Google_Client $googleClient)
    {
    }

    public function fetchAccessToken(Request $request): array
    {
        /* @phpstan-ignore-next-line */
        return $this->googleClient->fetchAccessTokenWithAuthCode($request->get('code'));
    }
}
