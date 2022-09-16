<?php

declare(strict_types=1);

namespace App\Video;

interface VideoInterface
{
    public function getYoutubeId(): string;

    public function setYoutubeId(string $youtubeId): void;

    public function getDefaultAudioLanguage(): string;

    public function getDefaultLanguage(): string;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getSeason(): int;

    public function setSeason(int $season): void;

    public function setEpisode(int $episode): void;

    public function getEpisode(): int;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    /**
     * @return array<array-key, string>
     */
    public function getTags(): array;

    /**
     * @param array<array-key, string> $tags
     */
    public function setTags(array $tags): void;

    public function getPrivacyStatus(): string;

    public function setPrivacyStatus(string $privacyStatus): void;

    public function getThumbnail(): string;

    public function setThumbnail(string $thumbnail): void;
}
