<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Type\StatusType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Video
{
    #[Column(type: StatusType::NAME, nullable: true)]
    private ?Status $status = null;

    #[Column(type: Types::INTEGER, nullable: true)]
    private ?int $views = null;

    #[Column(type: Types::INTEGER, nullable: true)]
    private ?int $likes = null;

    #[Column(type: Types::INTEGER, nullable: true)]
    private ?int $comments = null;

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(?int $views): void
    {
        $this->views = $views;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): void
    {
        $this->likes = $likes;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(?int $comments): void
    {
        $this->comments = $comments;
    }
}
