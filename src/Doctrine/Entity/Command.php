<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\CommandRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: CommandRepository::class)]
#[UniqueEntity(fields: ['category', 'name'])]
#[UniqueConstraint(name: 'command_category_name_unique', fields: ['category', 'name'])]
class Command
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column]
    #[NotBlank]
    private string $name;

    #[ManyToOne]
    #[JoinColumn(nullable: false)]
    private Category $category;

    #[Column]
    #[NotBlank]
    private string $template;

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

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}
