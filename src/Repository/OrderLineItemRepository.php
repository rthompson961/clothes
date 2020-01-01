<?php

namespace App\Repository;

use App\Entity\OrderLineItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OrderLineItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderLineItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderLineItem[]    findAll()
 * @method OrderLineItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderLineItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLineItem::class);
    }

    // /**
    //  * @return OrderLineItem[] Returns an array of OrderLineItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderLineItem
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
