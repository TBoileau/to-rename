<?php

declare(strict_types=1);

namespace App\OAuth;

use App\OAuth\Security\Provider\ProviderInterface;
use App\OAuth\Security\Token\TokenInterface;

interface ClientInterface
{
    public static function getName(): string;

    /**
     * @param string $redirectUri
     *
     * @return void
     */
    public function setRedirectUri($redirectUri);

    /**
     * @param string|array<array-key, string> $scopes
     *
     * @return void
     */
    public function setScopes($scopes);

    /**
     * @return string
     */
    public function createAuthUrl();

    /**
     * @param string|array<string, mixed> $accessToken
     *
     * @return void
     */
    public function setAccessToken($accessToken);

    /**
     * @return bool
     */
    public function isAccessTokenExpired();

    /**
     * @param string $code
     *
     * @return array<string, mixed>
     */
    public function fetchAccessTokenWithAuthCode($code);

    public function getProvider(): ProviderInterface;

    public function getToken(): TokenInterface;
}
