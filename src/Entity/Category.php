<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: CategoryRepository::class)]
class Category
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
     * @var array<string, ParameterType>
     */
    #[Column(type: Types::JSON)]
    private array $parameters = [];

    /**
     * @var array<array-key, string>
     */
    #[Column(type: Types::JSON)]
    private array $choices = [];

    /**
     * @var class-string|null
     */
    #[Column(type: Types::STRING, nullable: true)]
    private ?string $targetEntity = null;

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
     * @return array<string, ParameterType>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array<string, ParameterType> $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array<array-key, string>
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @param array<array-key, string> $choices
     */
    public function setChoices(array $choices): void
    {
        $this->choices = $choices;
    }

    /**
     * @return class-string|null
     */
    public function getTargetEntity(): ?string
    {
        return $this->targetEntity;
    }

    /**
     * @param class-string|null $targetEntity
     */
    public function setTargetEntity(?string $targetEntity): void
    {
        $this->targetEntity = $targetEntity;
    }
}
