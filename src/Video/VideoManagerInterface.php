<?php

declare(strict_types=1);

namespace App\Video;

use App\Entity\Video;
use Google\Service\YouTube\Video as YoutubeVideo;

interface VideoManagerInterface
{
    public function synchronize(): void;

    public function update(Video $video): void;

    public function hydrate(Video $video, ?YoutubeVideo $youtubeVideo = null): void;

    public function updateStatistics(): void;
}
