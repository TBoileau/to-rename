<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Type\StatusType;
use App\Repository\VideoRepository;
use App\Video\VideoInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: VideoRepository::class)]
class Video implements VideoInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::STRING)]
    private string $title;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::INTEGER)]
    private int $season = 0;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::INTEGER)]
    private int $episode = 0;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::TEXT)]
    private string $description;

    #[ManyToOne(targetEntity: Category::class)]
    #[JoinColumn(onDelete: 'SET NULL')]
    private ?Category $category = null;

    #[Column(type: Types::STRING)]
    private string $thumbnail;

    #[NotBlank(groups: ['create'])]
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

    #[Column(type: StatusType::NAME)]
    private Status $status = Status::Public;

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

    public function getThumbnail(): string
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function getDefaultAudioLanguage(): string
    {
        return 'FR';
    }

    public function getDefaultLanguage(): string
    {
        return 'FR';
    }

    public function getPrivacyStatus(): string
    {
        return $this->status->value;
    }

    public function setPrivacyStatus(string $privacyStatus): void
    {
        $this->status = Status::from($privacyStatus);
    }
}
