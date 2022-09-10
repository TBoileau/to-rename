<?php

declare(strict_types=1);

namespace App\Google\Security\Guard;

use App\Google\Security\Provider\ProviderInterface;
use Google_Client;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class GoogleAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private Google_Client $googleClient,
        private ProviderInterface $googleProvider
    ) {
    }

    public function authenticate(Request $request): void
    {
        $request->getSession()->set('google_access_token', $this->googleProvider->fetchAccessToken($request));
    }

    public function authorize(): RedirectResponse
    {
        return new RedirectResponse($this->googleClient->createAuthUrl());
    }
}
