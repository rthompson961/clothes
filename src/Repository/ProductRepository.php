<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method int|null     findProductCount(array $filters)
 * @method Product[]    findProducts(array $filters, string $sort, int $offset, int $limit)
 */
class ProductRepository extends ServiceEntityRepository
{
    public const ITEMS_PER_PAGE = 6;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductCount(?array $search, array $filters): ?int
    {
        $param = 1;
        $qb = $this->createQueryBuilder('p')->select('count(p.id)');

        if ($search) {
            foreach ($search as $word) {
                $qb->andWhere($qb->expr()->like('p.name', '?' . $param));
                $qb->setParameter($param, '%'. $word .'%');
                $param++;
            }
        }

        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $qb->andWhere($qb->expr()->in('p.' . $key, '?' . $param));
                $qb->setParameter($param, $filters[$key]);
                $param++;
            }
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findProducts(
        ?array $search,
        array $filters,
        string $sort,
        int $page
    ): ?array {
        $param = 1;
        $qb = $this->createQueryBuilder('p');

        if ($search) {
            foreach ($search as $word) {
                $qb->andWhere($qb->expr()->like('p.name', '?' . $param));
                $qb->setParameter($param, '%'. $word .'%');
                $param++;
            }
        }

        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $qb->andWhere($qb->expr()->in('p.' . $key, '?' . $param));
                $qb->setParameter($param, $filters[$key]);
                $param++;
            }
        }

        switch ($sort) {
            case 'name':
                $field = 'name';
                break;
            case 'low':
            case 'high':
                $field = 'price';
                break;
            default:
                $field = 'id';
                break;
        }
        $dir = $sort == 'high' ? 'DESC' : 'ASC';

        $offset = $page * self::ITEMS_PER_PAGE - self::ITEMS_PER_PAGE;

        $qb->orderBy('p.' . $field, $dir)
           ->setFirstResult($offset)
           ->setMaxResults(self::ITEMS_PER_PAGE);
                    
        return $qb->getQuery()->getResult();
    }
}
