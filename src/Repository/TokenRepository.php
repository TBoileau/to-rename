<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Token>
 */
final class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }
}
