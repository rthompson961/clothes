<?php

namespace App\Repository;

use App\Entity\ProductStockItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductStockItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductStockItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductStockItem[]    findAll()
 * @method ProductStockItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductStockItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductStockItem::class);
    }

    // /**
    //  * @return ProductStockItem[] Returns an array of ProductStockItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductStockItem
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
