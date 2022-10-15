<?php

declare(strict_types=1);

namespace App\UseCase\ScheduleNewsletter;

use App\Doctrine\Entity\Newsletter;

interface ScheduleNewsletterInterface
{
    public function send(Newsletter $newsletter): void;
}
