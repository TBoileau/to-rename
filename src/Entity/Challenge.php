<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChallengeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Live::class)]
    #[JoinColumn(nullable: false)]
    private Live $live;

    #[ManyToOne(targetEntity: Video::class)]
    private ?Video $video;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[Embedded(class: Duration::class)]
    private Duration $duration;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $startedAt = null;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $endedAt = null;

    #[Column(type: Types::INTEGER)]
    private int $basePoints;

    /**
     * @var Collection<int, ChallengeRule>
     */
    #[OneToMany(mappedBy: 'challenge', targetEntity: ChallengeRule::class)]
    private Collection $rules;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLive(): Live
    {
        return $this->live;
    }

    public function setLive(Live $live): void
    {
        $this->live = $live;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): void
    {
        $this->video = $video;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getEndedAt(): ?DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?DateTimeImmutable $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    /**
     * @return Collection<int, ChallengeRule>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function getBasePoints(): int
    {
        return $this->basePoints;
    }

    public function setBasePoints(int $basePoints): void
    {
        $this->basePoints = $basePoints;
    }

    public function isSucceed(): bool
    {
        return $this->basePoints - array_sum($this->getRules()
            ->map(static fn (ChallengeRule $challengeRule): int => $challengeRule->getHit() * $challengeRule->getRule()->getPoints())
            ->toArray()) > 0;
    }
}
