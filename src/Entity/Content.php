<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContentRepository;
use App\Video\VideoContentInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\OneToMany;
use Stringable;

#[Entity(repositoryClass: ContentRepository::class)]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'discr', type: Types::STRING)]
#[DiscriminatorMap([
    'challenge' => Challenge::class,
    'getting_started' => GettingStarted::class,
    'capsule' => Capsule::class,
    'code_review' => CodeReview::class,
    'project' => Project::class,
    'podcast' => Podcast::class,
    'kata' => Kata::class,
])]
abstract class Content implements VideoContentInterface, Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[Column(type: Types::STRING)]
    protected string $title;

    #[Column(type: Types::TEXT)]
    protected string $description;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    protected DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Live>
     */
    #[OneToMany(mappedBy: 'content', targetEntity: Live::class)]
    protected Collection $lives;

    abstract public static function getName(): string;

    abstract public static function getLogo(): string;

    public function __construct()
    {
        $this->lives = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<int, Live>
     */
    public function getLives(): Collection
    {
        return $this->lives;
    }

    public function __toString(): string
    {
        return $this->getVideoTitle();
    }
}
