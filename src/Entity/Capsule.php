<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CapsuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: CapsuleRepository::class)]
class Capsule extends Content
{
    #[Column(type: Types::STRING)]
    private string $repository;

    public static function getName(): string
    {
        return 'capsule';
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function setRepository(string $repository): void
    {
        $this->repository = $repository;
    }
}
