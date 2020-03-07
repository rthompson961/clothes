<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductCount(array $filters): ?int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)');

        if ($filters['category']) {
            $qb->andWhere($qb->expr()->in('p.category', $filters['category']));
        }

        if ($filters['brand']) {
            $qb->andWhere($qb->expr()->in('p.brand', $filters['brand']));
        }

        if ($filters['colour']) {
            $qb->andWhere($qb->expr()->in('p.colour', $filters['colour']));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findProducts(array $filters, string $sort, int $offset, int $limit): ?array
    {
        $qb = $this->createQueryBuilder('p');

        if ($filters['category']) {
            $qb->andWhere($qb->expr()->in('p.category', $filters['category']));
        }

        if ($filters['brand']) {
            $qb->andWhere($qb->expr()->in('p.brand', $filters['brand']));
        }

        if ($filters['colour']) {
            $qb->andWhere($qb->expr()->in('p.colour', $filters['colour']));
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
        $qb->orderBy('p.' . $field, $dir)
           ->setFirstResult($offset)
           ->setMaxResults($limit);
                    
        return $qb->getQuery()->getResult();
    }
}
