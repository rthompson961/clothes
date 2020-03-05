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
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductCount($filters)
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

    public function findProducts($filters, $sort, $offset, $limit)
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
