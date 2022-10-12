<?php

declare(strict_types=1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Planning>
 */
final class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }
}
