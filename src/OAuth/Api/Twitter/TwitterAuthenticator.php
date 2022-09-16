<?php

declare(strict_types=1);

namespace App\OAuth\Api\Twitter;

use App\OAuth\ClientInterface;
use App\OAuth\Security\Guard\AbstractOAuthAuthenticator;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TwitterAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        ClientInterface $twitterClient,
        TokenRepository $tokenRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($twitterClient, $tokenRepository, $entityManager);
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
