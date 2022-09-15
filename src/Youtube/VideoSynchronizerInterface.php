<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Entity\Video;
use Google\Service\YouTube\Video as YoutubeVideo;

interface VideoSynchronizerInterface
{
    public function syncOne(Video $video): void;

    public function syncAll(): void;
}
