<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductUnit;
use App\Entity\Size;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductUnit[]    findAll()
 * @method ProductUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductUnit::class);
    }

    public function findBasketUnits(array $basket): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id')
            ->addSelect('p.id as product_id')
            ->addSelect('p.name')
            ->addSelect('s.name as size')
            ->addSelect('p.price')
            ->addSelect('u.stock')
            ->innerJoin('u.product', 'p')
            ->innerJoin('u.size', 's');
        $qb->where($qb->expr()->in('u.id', $basket));
        $result = $qb->getQuery()->getArrayResult();

        return $result ?? [];
    }

    public function findBasketUnitObjects(array $basket): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where($qb->expr()->in('u.id', $basket));
        $result = $qb->getQuery()->getResult();

        return $result ?? [];
    }

    public function findProductUnits(int $id): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id')
            ->addSelect('u.stock')
            ->addSelect('s.name as size')
            ->innerJoin('u.product', 'p')
            ->innerJoin('u.size', 's')
            ->where('p.id = ?1')
            ->setParameter(1, $id)
            ->getQuery()
            ->getArrayResult();

        return $qb ?? [];
    }
}
