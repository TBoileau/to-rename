<?php

declare(strict_types=1);

namespace App\Video\Thumbnail;

use App\Entity\Video;

interface ThumbnailGeneratorInterface
{
    public function generate(Video $video): void;
}
