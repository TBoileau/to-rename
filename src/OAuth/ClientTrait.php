<?php

declare(strict_types=1);

namespace App\OAuth;

use App\OAuth\Security\Provider\OAuthProvider;
use App\OAuth\Security\Provider\ProviderInterface;
use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenInterface;

trait ClientTrait
{
    private ?ProviderInterface $provider = null;

    private ?TokenInterface $token = null;

    public function getProvider(): ProviderInterface
    {
        if (null === $this->provider) {
            $this->provider = new OAuthProvider($this);
        }

        return $this->provider;
    }

    public function getToken(): TokenInterface
    {
        if (null === $this->token) {
            $this->token = new OAuthToken($this);
        }

        return $this->token;
    }
}
