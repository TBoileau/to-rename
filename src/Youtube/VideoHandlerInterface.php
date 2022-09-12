<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Entity\Video;
use Google\Service\YouTube\Video as YoutubeVideo;

interface VideoHandlerInterface
{
    /**
     * @return iterable<int, YoutubeVideo>
     */
    public function list(): iterable;

    /**
     * @param array<array-key, string> $ids
     *
     * @return array<array-key, YoutubeVideo>
     */
    public function get(array $ids): array;

    public function update(Video $video): void;
}
