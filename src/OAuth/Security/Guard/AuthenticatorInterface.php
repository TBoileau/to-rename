<?php

declare(strict_types=1);

namespace App\OAuth\Security\Guard;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    public function authenticate(Request $request): void;

    public function refresh(Request $request): void;

    public function authorize(): RedirectResponse;

    public function setRedirectUri(): void;

    public static function getName(): string;
}
