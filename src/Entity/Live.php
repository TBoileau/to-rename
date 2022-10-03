<?php

declare(strict_types=1);

namespace App\Entity;

use App\Planning\PlanningContentInterface;
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
class Live implements Stringable, PlanningContentInterface
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

    public function __construct()
    {
        $this->duration = new Duration();
        $this->duration->setHours(2);
    }

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

    public function getLiveTitle(): string
    {
        return '';
    }
}
