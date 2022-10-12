<?php

declare(strict_types=1);

namespace App\Youtube;

use Google\Service\YouTube\Video;

interface VideoProviderInterface
{
    public function find(string $id): Video;

    public function hydrate(YoutubeVideoInterface $video): void;

    public function update(YoutubeVideoInterface $video): void;
}
