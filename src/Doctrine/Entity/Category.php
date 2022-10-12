<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Stringable;

#[Entity(repositoryClass: CategoryRepository::class)]
class Category implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::STRING, unique: true)]
    private string $name;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[Column(type: Types::STRING)]
    private string $image;

    #[Column(type: Types::TEXT)]
    private string $template;

    /**
     * @var array<array-key, string>
     */
    #[Column(type: Types::JSON)]
    private array $parameters = [];

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

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array<array-key, string>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array<array-key, string> $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
