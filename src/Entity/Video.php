<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

#[Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[NotBlank]
    #[Column(type: Types::STRING)]
    private string $title;

    #[NotBlank]
    #[GreaterThan(0)]
    #[Column(type: Types::INTEGER)]
    private int $season;

    #[NotBlank]
    #[GreaterThan(0)]
    #[Column(type: Types::INTEGER)]
    private int $episode;

    #[NotBlank]
    #[Column(type: Types::STRING)]
    private string $logo;

    #[Column(type: Types::STRING)]
    private string $thumbnail;

    #[Url]
    #[NotBlank]
    #[Column(type: Types::STRING)]
    private string $link;

    #[ManyToOne(targetEntity: Live::class)]
    private ?Live $live;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function setSeason(int $season): void
    {
        $this->season = $season;
    }

    public function getEpisode(): int
    {
        return $this->episode;
    }

    public function setEpisode(int $episode): void
    {
        $this->episode = $episode;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    public function getLive(): ?Live
    {
        return $this->live;
    }

    public function setLive(?Live $live): void
    {
        $this->live = $live;
    }
}
