<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Video
{
    #[Column(type: Types::STRING)]
    private string $privacyStatus;

    #[Column(type: Types::STRING)]
    private string $thumbnail;

    #[Column(type: Types::INTEGER)]
    private int $views;

    #[Column(type: Types::INTEGER)]
    private int $likes;

    #[Column(type: Types::INTEGER)]
    private int $comments;

    public function getPrivacyStatus(): string
    {
        return $this->privacyStatus;
    }

    public function setPrivacyStatus(string $privacyStatus): void
    {
        $this->privacyStatus = $privacyStatus;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }

    public function getComments(): int
    {
        return $this->comments;
    }

    public function setComments(int $comments): void
    {
        $this->comments = $comments;
    }
}
