<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Rule>
 */
final class RuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }

    /**
     * @return array<array-key, Rule>
     */
    public function get10RandomRules(): array
    {
        /** @var array<array-key, Rule> $rules */
        $rules = $this->createQueryBuilder('r')
            ->orderBy('RAND()')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $rules;
    }
}
