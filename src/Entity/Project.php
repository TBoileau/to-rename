<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: ProjectRepository::class)]
class Project extends Content
{
    #[Column(type: Types::STRING)]
    private string $repository;

    public static function getName(): string
    {
        return 'project';
    }

    public static function getLogo(): string
    {
        return 'project.png';
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function setRepository(string $repository): void
    {
        $this->repository = $repository;
    }

    public function getVideoDescription(): string
    {
        return <<<EOF
Projet {$this->title}
{$this->description}
{$this->repository}
EOF;
    }

    public function getVideoTitle(): string
    {
        return sprintf(
            'Projet - %s',
            $this->title
        );
    }
}
