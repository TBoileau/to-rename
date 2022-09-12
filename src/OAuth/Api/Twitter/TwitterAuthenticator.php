<?php

declare(strict_types=1);

namespace App\OAuth\Api\Twitter;

use App\OAuth\ClientInterface;
use App\OAuth\Security\Guard\AbstractOAuthAuthenticator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TwitterAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        ClientInterface $twitterClient
    ) {
        parent::__construct($twitterClient);
    }

    protected function getRedirectUri(): string
    {
        return $this->urlGenerator->generate(
            'twitter_check',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function getSessionKey(): string
    {
        return 'twitter_access_token';
    }

    public static function getName(): string
    {
        return 'twitter';
    }
}
