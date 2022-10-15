<?php

declare(strict_types=1);

namespace App\UseCase\ScheduleNewsletter;

interface ScheduleNewsletterInterface
{
    public function schedule(): void;
}
