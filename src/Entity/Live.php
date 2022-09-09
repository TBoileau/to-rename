<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Filter\WeekFilter;
use App\Repository\LiveRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;

use function Symfony\Component\String\u;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity(repositoryClass: LiveRepository::class)]
#[UniqueEntity(
    fields: 'startedAt',
    message: 'Ce live existe déjà.',
    repositoryMethod: 'findByStartedAt'
)]
class Live
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
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
