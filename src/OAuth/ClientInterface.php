<?php

declare(strict_types=1);

namespace App\OAuth;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ClientInterface
{
    public static function getName(): string;

    public static function getSessionKey(): string;

    public function initRedirectUri(string $host, UrlGeneratorInterface $urlGenerator): void;

    /**
     * @param string $redirectUri
     *
     * @return void
     */
    public function setRedirectUri($redirectUri);

    /**
     * @param array<array-key, string> $scopes
     *
     * @return void
     */
    public function setScopes($scopes);

    /**
     * @return string
     */
    public function createAuthUrl();

    /**
     * @param array{created: int, access_token: string, expires_in: int, refresh_token: string} $accessToken
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

    /**
     * @param string $code
     *
     * @return array<string, mixed>
     */
    public function fetchAccessTokenWithRefreshToken($code);
}
