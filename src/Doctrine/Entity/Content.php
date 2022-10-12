<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\ContentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Stringable;

#[Entity(repositoryClass: ContentRepository::class)]
class Content implements Stringable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::STRING)]
    private string $title;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ManyToOne(targetEntity: Category::class)]
    #[JoinColumn(nullable: false)]
    private Category $category;

    /**
     * @var array{name: string, value: string}
     */
    #[Column(type: Types::JSON)]
    private array $parameters = [];

    /**
     * @var Collection<int, Live>
     */
    #[OneToMany(mappedBy: 'content', targetEntity: Live::class)]
    private Collection $lives;

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

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->parameters = array_map(
            static fn (string $name): array => ['name' => $name, 'value' => ''],
            $category->getParameters()
        );

        $this->category = $category;
    }

    /**
     * @return Collection<int, Live>
     */
    public function getLives(): Collection
    {
        return $this->lives;
    }

    /**
     * @return array{name: string, value: string}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array{name: string, value: string} $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameter(string $name): string
    {
        return $this->parameters[array_search($name, array_column($this->parameters, 'name'), true)]['value'];
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->category, $this->title);
    }
}
