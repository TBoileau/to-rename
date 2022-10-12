<?php

declare(strict_types=1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Challenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Challenge>
 */
final class ChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Challenge::class);
    }
}
