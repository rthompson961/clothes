<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductUnit;
use App\Entity\Size;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
