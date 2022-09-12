<?php

declare(strict_types=1);

namespace App\OAuth\Security\Guard;

use App\OAuth\ClientInterface;
use App\OAuth\Security\Provider\ProviderInterface;
use App\OAuth\Security\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractOAuthAuthenticator implements AuthenticatorInterface
{
    private ProviderInterface $provider;

    private TokenInterface $token;

    public function __construct(private ClientInterface $client)
    {
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

        if (null === $token) {
            return;
        }

        if (isset($token['error'])) {
            $session->remove($this->getSessionKey());

            return;
        }

        $this->token->save($token);
    }

    public function authenticate(Request $request): void
    {
        $request->getSession()->set($this->getSessionKey(), $this->provider->fetchAccessToken($request));
    }

    public function authorize(): RedirectResponse
    {
        return new RedirectResponse($this->client->createAuthUrl());
    }

    abstract protected function getRedirectUri(): string;

    abstract protected function getSessionKey(): string;
}
