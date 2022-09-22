<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\KataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: KataRepository::class)]
class Kata extends Content
{
    #[Column(type: Types::STRING)]
    private string $repository;

    public static function getName(): string
    {
        return 'kata';
    }

    public static function getLogo(): string
    {
        return 'kata.png';
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
Kata {$this->title}
{$this->description}
{$this->repository}
EOF;
    }

    public function getVideoTitle(): string
    {
        return sprintf(
            'Kata - %s',
            $this->title
        );
    }
}
