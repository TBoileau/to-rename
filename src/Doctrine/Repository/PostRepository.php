<?php

declare(strict_types=1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Post;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Post>
 */
final class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[]
     */
    public function getPostsFrom(DateTimeImmutable $date): array
    {
        /** @var array<Post> $posts */
        $posts = $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();

        return $posts;
    }
}
