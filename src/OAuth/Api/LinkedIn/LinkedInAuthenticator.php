<?php

declare(strict_types=1);

namespace App\OAuth\Api\LinkedIn;

use App\OAuth\ClientInterface;
use App\OAuth\Security\Guard\AbstractOAuthAuthenticator;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LinkedInAuthenticator extends AbstractOAuthAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        ClientInterface $linkedinClient,
        TokenRepository $tokenRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($linkedinClient, $tokenRepository, $entityManager);
    }

    protected function getRedirectUri(): string
    {
        return $this->urlGenerator->generate(
            'linkedin_check',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    protected function getSessionKey(): string
    {
        return 'linkedin_access_token';
    }

    public static function getName(): string
    {
        return 'linkedin';
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(array $accessToken): void
    {
        /** @var LinkedInClient $client */
        $client = $this->client;

        /** @var string $token */
        $token = $accessToken['access_token'];

        putenv(
            sprintf(
                'LINKEDIN_DSN=linkedin://%s:%s@default',
                $token,
                $client->getProfileId()
            )
        );
    }
}
