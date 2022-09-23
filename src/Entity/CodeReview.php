<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CodeReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: CodeReviewRepository::class)]
class CodeReview extends Content
{
    #[Column(type: Types::STRING)]
    private string $repository;

    public static function getName(): string
    {
        return 'code_review';
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
Code Review {$this->title}
{$this->description}
{$this->repository}
EOF;
    }

    public function getVideoTitle(): string
    {
        return sprintf(
            'Code Review - %s',
            $this->title
        );
    }
}
