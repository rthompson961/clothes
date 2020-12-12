<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    private const LIMIT = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrdersByUser(User $user): ?array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.address', 'a')
            ->select('o.id')
            ->addSelect('o.total')
            ->addSelect('a.address1')
            ->addSelect('a.address2')
            ->addSelect('a.county')
            ->addSelect('a.postcode')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(self::LIMIT)
            ->getQuery()
            ->getResult()
        ;
    }
}
