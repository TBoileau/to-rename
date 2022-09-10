<?php

declare(strict_types=1);

namespace App\Google\Security\Guard;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    public function authenticate(Request $request): void;

    public function authorize(): RedirectResponse;
}
