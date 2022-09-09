<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlanningRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use IntlDateFormatter;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity(repositoryClass: PlanningRepository::class)]
#[UniqueEntity(
    fields: 'startedAt',
    message: 'Ce planning existe déjà.',
)]
class Planning implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * @var Collection<int, Live>
     */
    #[OneToMany(mappedBy: 'planning', targetEntity: Live::class)]
    private Collection $lives;

    #[NotBlank]
    #[Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $startedAt;

    #[Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $endedAt;

    #[Column(type: Types::STRING)]
    private string $image;

    public function __construct()
    {
        $this->lives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Live>
     */
    public function getLives(): Collection
    {
        return $this->lives;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTimeImmutable $startedAt): void
    {
        $this->startedAt = $startedAt;
        $this->endedAt = $startedAt->add(new DateInterval('P4D'));
    }

    public function getEndedAt(): DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function __toString(): string
    {
        $start = (int) $this->startedAt->format('j');

        $end = (int) $this->endedAt->format('j');

        return sprintf(
            'du %d au %d %s',
            $start,
            $end,
            IntlDateFormatter::formatObject($this->startedAt, 'MMMM', 'fr_FR')
        );
    }

    public function getLiveByDate(DateTimeImmutable $date): ?Live
    {
        foreach ($this->lives as $live) {
            if ($live->getLivedAt()->format('Y-m-d') === $date->format('Y-m-d')) {
                return $live;
            }
        }

        return null;
    }

    #[Callback]
    public function checkIfStartDateIsAMonday(ExecutionContextInterface $context): void
    {
        if (1 !== (int) $this->startedAt->format('N')) {
            $context->buildViolation('La planning doit commencer un Lundi.')
                ->atPath('startedAt')
                ->addViolation();
        }
    }
}
