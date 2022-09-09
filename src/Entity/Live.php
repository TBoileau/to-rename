<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LiveRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use function Symfony\Component\String\u;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity(repositoryClass: LiveRepository::class)]
#[UniqueEntity(fields: 'livedAt', message: 'Ce live existe déjà.')]
class Live implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeInterface $livedAt;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[ManyToOne(targetEntity: Planning::class, inversedBy: 'lives')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Planning $planning;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivedAt(): DateTimeInterface
    {
        return $this->livedAt;
    }

    public function setLivedAt(DateTimeInterface $livedAt): void
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
}
