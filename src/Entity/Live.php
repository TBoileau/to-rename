<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LiveRepository;
use App\Video\VideoContentInterface;
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

use function Symfony\Component\String\u;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity(repositoryClass: LiveRepository::class)]
#[UniqueEntity(fields: 'livedAt', message: 'Ce live existe déjà.')]
class Live implements Stringable, VideoContentInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $livedAt;

    #[NotNull]
    #[Valid]
    #[Embedded(class: Duration::class)]
    private Duration $duration;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[ManyToOne(targetEntity: Planning::class, inversedBy: 'lives')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Planning $planning;

    #[ManyToOne(targetEntity: Content::class, inversedBy: 'lives')]
    #[JoinColumn(onDelete: 'SET NULL')]
    private ?Content $content;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::INTEGER)]
    private int $season = 0;

    #[NotBlank(groups: ['update'])]
    #[Column(type: Types::INTEGER)]
    private int $episode = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivedAt(): DateTimeImmutable
    {
        return $this->livedAt;
    }

    public function setLivedAt(DateTimeImmutable $livedAt): void
    {
        $this->livedAt = $livedAt;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
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
    public function checkDescription(ExecutionContextInterface $context): void
    {
        if (u(u($this->description)->wordwrap(15, "\n", false)->toString())->width() > 15) {
            $context->buildViolation('Chaque ligne doit faire 15 caractères maximum')
                ->atPath('description')
                ->addViolation();
        }
    }

    #[Callback]
    public function checkIfDate(ExecutionContextInterface $context): void
    {
        if ($this->livedAt < $this->planning->getStartedAt() || $this->livedAt > $this->planning->getEndedAt()) {
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

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): void
    {
        $this->content = $content;
    }

    public function getVideoDescription(): string
    {
        $date = $this->livedAt->format('d/m/Y');

        $season = sprintf('%02d', $this->season);
        $episode = sprintf('%02d', $this->episode);

        if (null === $this->content) {
            return <<<EOF
Saison {$season} Episode {$episode}
Rediffusion du live Twitch du {$date}.
{$this->description}
EOF;
        }

        return <<<EOF
Saison {$season} Episode {$episode}
Rediffusion du live Twitch du {$date}.
{$this->content->getVideoDescription()}
EOF;
    }

    public function getVideoTitle(): string
    {
        $date = $this->livedAt->format('d/m/Y');

        if (null === $this->content) {
            return sprintf('S%02dE%02d - Rediffusion du live Twitch du %s', $this->season, $this->episode, $date);
        }

        return sprintf('S%02dE%02d - %s', $this->season, $this->episode, $this->content->getVideoTitle());
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
