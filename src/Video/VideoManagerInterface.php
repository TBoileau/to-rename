<?php

declare(strict_types=1);

namespace App\Video;

use Google\Service\YouTube\Video as YoutubeVideo;

interface VideoManagerInterface
{
    public function synchronize(): void;

    public function update(VideoInterface $video): void;

    public function hydrate(VideoInterface $video, ?YoutubeVideo $youtubeVideo = null): void;

    public function updateStatistics(): void;
}
