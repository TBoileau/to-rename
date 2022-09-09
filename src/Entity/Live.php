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
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use function Symfony\Component\String\u;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity(repositoryClass: LiveRepository::class)]
#[UniqueEntity(
    fields: 'startedAt',
    message: 'Ce live existe déjà.',
    repositoryMethod: 'findByStartedAt'
)]
class Live implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeInterface $startedAt;

    #[Column(type: Types::TEXT)]
    private string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTimeInterface $startedAt): void
    {
        $this->startedAt = $startedAt;
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
            $this->startedAt->format('d/m/Y')
        );
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
}
