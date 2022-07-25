<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Live;
use App\Entity\Week;
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

    /**
     * @return array<Live>
     */
    public function getWeekLivesByLive(Live $live): array
    {
        /** @var array<Live> $lives */
        $lives = $this->createQueryBuilder('l')
            ->where('WEEK(l.startedAt) = :week')
            ->andWhere('YEAR(l.startedAt) = :year')
            ->setParameters([
                'week' => intval($live->getStartedAt()->format('W')),
                'year' => intval($live->getStartedAt()->format('Y')),
            ])
            ->getQuery()
            ->getResult();

        return $lives;
    }

    public function getWeekByLive(Live $live): Week
    {
        return new Week($this->getWeekLivesByLive($live));
    }
}
