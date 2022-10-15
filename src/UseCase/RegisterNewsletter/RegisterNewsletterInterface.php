<?php

declare(strict_types=1);

namespace App\UseCase\RegisterNewsletter;

interface RegisterNewsletterInterface
{
    public function register(string $email): void;
}
