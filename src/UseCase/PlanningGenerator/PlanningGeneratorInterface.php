<?php

declare(strict_types=1);

namespace App\UseCase\PlanningGenerator;

use App\Doctrine\Entity\Planning;

interface PlanningGeneratorInterface
{
    public function generate(Planning $planning): void;
}
