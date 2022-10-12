<?php

declare(strict_types=1);

namespace App\Youtube;

interface YoutubeVideoInterface
{
    public function getYoutubeId(): ?string;

    public function getVideoTitle(): string;

    public function getVideoDescription(): string;

    /**
     * @return array<array-key, string>
     */
    public function getVideoTags(): array;

    public function getVideoStatus(): string;

    public function getVideoThumbnail(): string;

    public function setVideo(string $videoStatus, int $views, int $likes, int $comments): void;
}
