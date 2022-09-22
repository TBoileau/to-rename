<?php

declare(strict_types=1);

namespace App\DataCollector;

use App\Entity\Video;

interface VideoCollectInterface
{
    /**
     * @return array<array-key, Video>
     */
    public function getVideosUpdated(): array;
}
