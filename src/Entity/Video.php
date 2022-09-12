<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

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
    #[Column(type: Types::INTEGER)]
    private int $season = 0;

    #[NotBlank]
    #[GreaterThan(0)]
    #[Column(type: Types::INTEGER)]
    private int $episode = 0;

    #[NotBlank]
    #[Column(type: Types::TEXT)]
    private string $description;

    #[ManyToOne(targetEntity: Logo::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    private ?Logo $logo = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $thumbnail = null;

    /**
     * @var array<string, string>
     */
    #[Column(type: Types::JSON)]
    private array $thumbnails = [];

    #[NotBlank]
    #[Column(type: Types::STRING)]
    private string $youtubeId;

    /**
     * @var array<array-key, string>
     */
    #[Column(type: Types::JSON)]
    private array $tags = [];

    #[ManyToOne(targetEntity: Live::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getYoutubeId(): string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(string $youtubeId): void
    {
        $this->youtubeId = $youtubeId;
    }

    public function getLive(): ?Live
    {
        return $this->live;
    }

    public function setLive(?Live $live): void
    {
        $this->live = $live;
    }

    public function getLogo(): ?Logo
    {
        return $this->logo;
    }

    public function setLogo(Logo $logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return array<array-key, string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array<array-key, string> $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return array<string, string>
     */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    /**
     * @param array<string, string> $thumbnails
     */
    public function setThumbnails(array $thumbnails): void
    {
        $this->thumbnails = $thumbnails;
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
}
