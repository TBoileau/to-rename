<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Stringable;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: CategoryRepository::class)]
class Category implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[NotBlank]
    #[Column(type: Types::STRING)]
    private string $name;

    #[NotBlank]
    #[Column(type: Types::STRING)]
    private string $image;

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
        return $this->name;
    }
}
