<?php

declare(strict_types=1);

namespace App\Generator;

use App\Entity\Planning;

interface PlanningGeneratorInterface
{
    public function generate(Planning $planning): void;
}
