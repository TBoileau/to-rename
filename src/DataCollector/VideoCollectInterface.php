<?php

declare(strict_types=1);

namespace App\DataCollector;

use App\Video\VideoInterface;

interface VideoCollectInterface
{
    /**
     * @return array<array-key, VideoInterface>
     */
    public function getVideosUpdated(): array;
}
