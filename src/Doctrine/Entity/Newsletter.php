<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;

#[Entity]
class Newsletter
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column]
    private DateTimeImmutable $scheduledAt;

    /**
     * @var Collection<int, Post>
     */
    #[ManyToMany(targetEntity: Post::class)]
    #[JoinTable(name: 'newsletter_posts')]
    private Collection $posts;

    /**
     * @var Collection<int, Live>
     */
    #[ManyToMany(targetEntity: Live::class)]
    #[JoinTable(name: 'newsletter_lives')]
    private Collection $lives;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->lives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScheduledAt(): DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(DateTimeImmutable $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return Collection<int, Live>
     */
    public function getLives(): Collection
    {
        return $this->lives;
    }
}
