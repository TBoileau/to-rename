<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Type\StatusType;
use App\Repository\VideoRepository;
use App\Video\VideoContentInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: VideoRepository::class)]
class Video implements VideoContentInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::STRING)]
    private string $title;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::TEXT)]
    private string $description;

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

    #[Column(type: Types::INTEGER)]
    private int $views = 0;

    #[Column(type: Types::INTEGER)]
    private int $likes = 0;

    #[Column(type: Types::INTEGER)]
    private int $comments = 0;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $logo = null;

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

    public function __toString(): string
    {
        return $this->title;
    }

    public function getVideoDescription(): string
    {
        if (null !== $this->live) {
            return <<<EOF
{$this->live->getVideoDescription()}

Twitter : https://twitter.com/boileau_thomas
Youtube : https://youtube.com/ThomasBoileau
Discord : https://discord.gg/toham
Github : https://github.com/TBoileau
EOF;
        }

        return <<<EOF
{$this->description}

Twitter : https://twitter.com/boileau_thomas
Youtube : https://youtube.com/ThomasBoileau
Discord : https://discord.gg/toham
Github : https://github.com/TBoileau
EOF;
    }

    public function getVideoTitle(): string
    {
        if (null !== $this->live) {
            return $this->live->getVideoTitle();
        }

        return $this->title;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): void
    {
        $this->logo = $logo;
    }
}
