<?php

declare(strict_types=1);

namespace App\Generator;

use App\Entity\Week;

interface PlanningGeneratorInterface
{
    public function generate(string $filename, Week $week): void;
}
