<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItem[]    findAll()
 * @method OrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function findItemsByOrder(Order $order): ?array
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.productUnit', 'u')
            ->innerJoin('u.product', 'p')
            ->innerJoin('u.size', 's')
            ->select('i.price')
            ->addSelect('i.quantity')
            ->addSelect('p.id as product_id')
            ->addSelect('p.name')
            ->addSelect('s.name as size')
            ->where('i.order = :order')
            ->setParameter('order', $order)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
