<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findLatestByCategory(Category $category, int $limit): array
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.categories', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->orderBy('n.insertDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByCategoryPaginated(Category $category, int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('n')
            ->innerJoin('n.categories', 'c')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->orderBy('n.insertDate', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery();

        return new Paginator($query);
    }

    public function findTopNewsByWeek(int $limit = 10): array
    {
        $oneWeekAgo = new \DateTime('-1 week');

        return $this->createQueryBuilder('n')
            ->leftJoin('n.comments', 'c')
            ->andWhere('n.insertDate >= :oneWeekAgo')
            ->setParameter('oneWeekAgo', $oneWeekAgo)
            ->groupBy('n.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
