<?php

declare(strict_types=1);

namespace App\Planning;

use App\Entity\Planning;

interface PlanningGeneratorInterface
{
    public function generate(Planning $planning): void;
}
