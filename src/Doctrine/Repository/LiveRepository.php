<?php

declare(strict_types=1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Live;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Live>
 */
final class LiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Live::class);
    }
}
