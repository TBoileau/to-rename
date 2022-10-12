<?php

declare(strict_types=1);

namespace App\UseCase\ThumbnailGenerator;

use App\Doctrine\Entity\Live;

interface ThumbnailGeneratorInterface
{
    public function generate(Live $live): void;
}
