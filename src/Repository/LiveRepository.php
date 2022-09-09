<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Live;
use DateTimeInterface;
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

    /**
     * @param array{startedAt: DateTimeInterface} $data
     *
     * @return array<Live>
     */
    public function findByStartedAt(array $data): array
    {
        /** @var array<Live> $lives */
        $lives = $this->createQueryBuilder('l')
            ->where('DATE(l.startedAt) = :startedAt')
            ->setParameter('startedAt', $data['startedAt']->format('Y-m-d'))
            ->getQuery()
            ->getResult();

        return $lives;
    }
}
