<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    public function findUserAddresses(User $user): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('a.id')
            ->addSelect('a.address1')
            ->addSelect('a.address2')
            ->addSelect('a.postcode')
            ->where('a.user = ?1')
            ->setParameter(1, $user)
            ->getQuery()
            ->getArrayResult();

        return $result ?? [];
    }
}
