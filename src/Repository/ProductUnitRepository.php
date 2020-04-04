<?php

namespace App\Repository;

use App\Entity\ProductUnit;
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

    // /**
    //  * @return ProductUnit[] Returns an array of ProductUnit objects
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
    public function findOneBySomeField($value): ?ProductUnit
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
