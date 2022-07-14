<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Filter\WeekFilter;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;

#[Entity]
#[ApiResource(
    collectionOperations: [Request::METHOD_POST, Request::METHOD_GET],
    itemOperations: [Request::METHOD_GET, Request::METHOD_PUT, Request::METHOD_DELETE],
    attributes: ['pagination_enabled' => false]
)]
#[ApiFilter(WeekFilter::class, properties: ['startedAt'])]
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
}
