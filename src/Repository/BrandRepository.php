<?php

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method array      findAllAsArray()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }

    public function findAllAsArray(): array
    {
        $result = $this->createQueryBuilder('b')
            ->getQuery()
            ->getArrayResult();

        return $result ?? [];
    }
}
