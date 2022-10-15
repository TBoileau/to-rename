<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\LiveRepository;
use App\SendinBlue\SendinBlueItemInterface;
use App\Youtube\YoutubeVideoInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity(repositoryClass: LiveRepository::class)]
#[UniqueEntity(fields: 'livedAt', message: 'Ce live existe déjà.')]
class Live implements Stringable, YoutubeVideoInterface, SendinBlueItemInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::STRING)]
    private string $thumbnail;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $livedAt;

    #[NotNull]
    #[Valid]
    #[Embedded(class: Duration::class)]
    private Duration $duration;

    #[ManyToOne(targetEntity: Planning::class, inversedBy: 'lives')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Planning $planning;

    #[ManyToOne(targetEntity: Content::class, inversedBy: 'lives')]
    #[JoinColumn(nullable: false)]
    private Content $content;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::INTEGER)]
    private int $season = 0;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::INTEGER)]
    private int $episode = 0;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $youtubeId = null;

    #[Embedded]
    private ?Video $video = null;

    public function __construct()
    {
        $this->duration = new Duration();
        $this->duration->setHours(2);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getLivedAt(): DateTimeImmutable
    {
        return $this->livedAt;
    }

    public function setLivedAt(DateTimeImmutable $livedAt): void
    {
        $this->livedAt = $livedAt;
    }

    public function __toString(): string
    {
        return sprintf(
            'Live du %s',
            $this->livedAt->format('d/m/Y')
        );
    }

    public function getPlanning(): Planning
    {
        return $this->planning;
    }

    public function setPlanning(Planning $planning): void
    {
        $this->planning = $planning;
        $this->planning->getLives()->add($this);
    }

    #[Callback]
    public function checkIfDate(ExecutionContextInterface $context): void
    {
        if (
            $this->livedAt < $this->planning->getStartedAt()->setTime(0, 0)
            || $this->livedAt > $this->planning->getEndedAt()->setTime(23, 59, 59)
        ) {
            $context->buildViolation('La date du live doit être comprise entre le début et la fin du planning')
                ->atPath('livedAt')
                ->addViolation();
        }
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
    }

    public function getEndedAt(): DateTimeImmutable
    {
        return $this->duration->addTo($this->livedAt);
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): void
    {
        $this->content = $content;
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

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(?string $youtubeId): void
    {
        $this->youtubeId = $youtubeId;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function getPlanningTitle(): string
    {
        return sprintf(
            '%s %s',
            $this->content->getCategory()->getName(),
            $this->content->getTitle()
        );
    }

    public function getVideoTitle(): string
    {
        return sprintf(
            'S%02dE%02d - %s - %s',
            $this->season,
            $this->episode,
            $this->content->getCategory()->getName(),
            $this->content->getTitle()
        );
    }

    public function getVideoDescription(): string
    {
        return $this->content->getCategory()->getTemplate();
    }

    public function getVideoTags(): array
    {
        return array_merge(
            [
                $this->content->getCategory()->getName(),
                'twitch',
            ],
            explode(',', $this->getContent()->getParameter('tags'))
        );
    }

    public function getVideoStatus(): string
    {
        /** @var Video $video */
        $video = $this->video;

        /** @var Status $status */
        $status = $video->getStatus();

        return $status->value;
    }

    public function getVideoThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setVideo(string $videoStatus, int $views, int $likes, int $comments): void
    {
        $this->video = new Video();
        $this->video->setStatus(Status::from($videoStatus));
        $this->video->setViews($views);
        $this->video->setLikes($likes);
        $this->video->setComments($comments);
    }

    public function getItemTitle(): string
    {
        return $this->getVideoTitle();
    }

    public function getItemDescription(): string
    {
        return sprintf(
            'Rediffusion du %s. %s',
            $this->livedAt->format('d/m/Y'),
            $this->getContent()->getDescription()
        );
    }

    public function getItemImage(): string
    {
        return $this->thumbnail;
    }

    public function getItemUrl(): string
    {
        return sprintf('/live/%d', $this->id);
    }
}
