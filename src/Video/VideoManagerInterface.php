<?php

declare(strict_types=1);

namespace App\Video;

use App\Entity\Live;

interface VideoManagerInterface
{
    public function hydrate(Live $live): void;
}
