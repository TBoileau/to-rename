<?php

declare(strict_types=1);

namespace App\OAuth\Api\Google;

use App\OAuth\ClientInterface;
use App\OAuth\Security\Guard\AbstractOAuthAuthenticator;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class GoogleAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        ClientInterface $googleClient,
        TokenRepository $tokenRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($googleClient, $tokenRepository, $entityManager);
    }

    protected function getRedirectUri(): string
    {
        return $this->urlGenerator->generate(
            'google_check',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function getSessionKey(): string
    {
        return 'google_access_token';
    }

    public static function getName(): string
    {
        return 'google';
    }
}