<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GettingStartedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: GettingStartedRepository::class)]
class GettingStarted extends Content
{
    #[Column(type: Types::STRING)]
    private string $repository;

    public static function getName(): string
    {
        return 'getting_started';
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
Getting Started {$this->title}
{$this->description}
{$this->repository}
EOF;
    }

    public function getVideoTitle(): string
    {
        return sprintf(
            'Getting Started - %s',
            $this->title
        );
    }
}
