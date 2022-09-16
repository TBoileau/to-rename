<?php

declare(strict_types=1);

namespace App\OAuth\Security\Guard;

use App\Entity\Token;
use App\OAuth\ClientInterface;
use App\OAuth\Security\Provider\ProviderInterface;
use App\OAuth\Security\Token\TokenInterface;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractOAuthAuthenticator implements AuthenticatorInterface
{
    protected ProviderInterface $provider;

    protected TokenInterface $token;

    public function __construct(
        private ClientInterface $client,
        private TokenRepository $tokenRepository,
        private EntityManagerInterface $entityManager
    ) {
        $this->provider = $this->client->getProvider();
        $this->token = $this->client->getToken();
        $this->setRedirectUri();
    }

    public function setRedirectUri(): void
    {
        $this->client->setRedirectUri($this->getRedirectUri());
    }

    public function refresh(Request $request): void
    {
        $session = $request->getSession();

        /** @var array<string, mixed>|null $token */
        $token = $session->get($this->getSessionKey());

        if (null !== $token) {
            $this->token->save($token);
        }

        if (null === $token || !$this->token->isAuthenticated()) {
            /** @var Token $token */
            $token = $this->tokenRepository->findOneBy(['name' => static::getName()]);

            if (null === $token->getRefreshToken()) {
                return;
            }

            $accessToken = $this->provider->fetchAccessTokenWithRefreshToken($token->getRefreshToken());

            $session->set($this->getSessionKey(), $accessToken);

            $this->token->save($accessToken);
        }
    }

    public function authenticate(Request $request): void
    {
        $accessToken = $this->provider->fetchAccessToken($request);

        /** @var Token $token */
        $token = $this->tokenRepository->findOneBy(['name' => static::getName()]);

        /** @var string $refreshToken */
        $refreshToken = $accessToken['refresh_token'];

        $token->setRefreshToken($refreshToken);

        $this->entityManager->flush();

        $request->getSession()->set($this->getSessionKey(), $accessToken);
    }

    public function authorize(): RedirectResponse
    {
        return new RedirectResponse($this->client->createAuthUrl());
    }

    abstract protected function getRedirectUri(): string;

    abstract protected function getSessionKey(): string;
}
