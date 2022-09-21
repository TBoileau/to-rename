<?php

declare(strict_types=1);

namespace App\OAuth;

use Google\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class GoogleClient extends Client implements ClientInterface
{
    public function initRedirectUri(string $host, UrlGeneratorInterface $urlGenerator): void
    {
        $this->setRedirectUri(
            sprintf(
                '%s%s',
                $host,
                $urlGenerator->generate('google_check')
            )
        );
    }

    public static function getName(): string
    {
        return 'google';
    }

    public static function getSessionKey(): string
    {
        return 'google_access_token';
    }
}
