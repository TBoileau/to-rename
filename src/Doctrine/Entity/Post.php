<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity('slug')]
class Post implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column]
    private string $state = 'draft';

    #[Column]
    private string $title;

    #[Column(unique: true)]
    private string $slug;

    #[Column(type: Types::TEXT)]
    private string $content;

    #[Column]
    private string $cover;

    #[Column]
    private DateTimeImmutable $createdAt;

    #[Column(nullable: true)]
    private ?DateTimeImmutable $publishedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCover(): string
    {
        return $this->cover;
    }

    public function setCover(string $cover): void
    {
        $this->cover = $cover;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
