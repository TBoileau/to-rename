<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\ChallengeRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use LogicException;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

#[Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::STRING)]
    private string $name;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[NotNull]
    #[Valid]
    #[Embedded(class: Duration::class)]
    private Duration $duration;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $startedAt = null;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $endedAt = null;

    #[NotBlank]
    #[GreaterThan(0)]
    #[Column(type: Types::INTEGER)]
    private int $basePoints = 30;

    /**
     * @var Collection<int, ChallengeRule>
     */
    #[OneToMany(mappedBy: 'challenge', targetEntity: ChallengeRule::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $rules;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->duration = new Duration();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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

    public function getBasePoints(): int
    {
        return $this->basePoints;
    }

    public function setBasePoints(int $basePoints): void
    {
        $this->basePoints = $basePoints;
    }

    /**
     * @return Collection<int, ChallengeRule>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(ChallengeRule $rule): void
    {
        $rule->setChallenge($this);
        $this->rules->add($rule);
    }

    public function removeRule(ChallengeRule $rule): void
    {
        $rule->setChallenge(null);
        $this->rules->removeElement($rule);
    }

    public function isSucceed(): bool
    {
        return null !== $this->endedAt && $this->getFinalPoints() >= 0;
    }

    public function getTotalPoints(): int
    {
        return intval(
            array_sum(
                $this->getRules()
                        ->map(static fn (ChallengeRule $challengeRule): int => $challengeRule->getTotal())
                        ->toArray()
            )
        );
    }

    public function getFinalPoints(): int
    {
        return $this->basePoints + $this->getTotalPoints();
    }

    public function getTheoreticalEndDate(): DateTimeImmutable
    {
        if (null === $this->startedAt) {
            throw new LogicException('The challenge must be started to get the theoretical end date.');
        }

        return $this->duration->addTo($this->startedAt);
    }

    public function getDiff(): ?DateInterval
    {
        if (null === $this->endedAt || null === $this->startedAt) {
            return null;
        }

        return $this->endedAt->diff($this->startedAt);
    }
}
