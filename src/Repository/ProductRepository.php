<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method int|null     findProductCount(array $filters)
 * @method Product[]    findProducts(array $filters, string $sort, int $offset, int $limit)
 */
class ProductRepository extends ServiceEntityRepository
{
    public const ITEMS_PER_PAGE = 6;
    private int $param = 1;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductCount(array $input): ?int
    {
        $qb = $this->createQueryBuilder('p')->select('count(p.id)');
        $qb = $this->addSearchQueryBuilder($qb, $input['search']);
        $qb = $this->addFiltersQueryBuilder($qb, $input['filters']);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findProducts(array $input): ?array
    {
        $qb = $this->createQueryBuilder('p');
        $qb = $this->addSearchQueryBuilder($qb, $input['search']);
        $qb = $this->addFiltersQueryBuilder($qb, $input['filters']);

        switch ($input['sort']) {
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
        $dir = $input['sort'] == 'high' ? 'DESC' : 'ASC';

        $offset = $input['page']* self::ITEMS_PER_PAGE - self::ITEMS_PER_PAGE;

        $qb->orderBy('p.' . $field, $dir)
           ->setFirstResult($offset)
           ->setMaxResults(self::ITEMS_PER_PAGE);
                    
        return $qb->getQuery()->getResult();
    }

    public function addSearchQueryBuilder(QueryBuilder $qb, ?string $search) : QueryBuilder
    {
        if ($search) {
            $search = explode(' ', $search);

            foreach ($search as $word) {
                $qb->andWhere($qb->expr()->like('p.name', '?' . $this->param));
                $qb->setParameter($this->param++, '%'. $word .'%');
            }
        }

        return $qb;
    }

    public function addFiltersQueryBuilder(QueryBuilder $qb, array $filters) : QueryBuilder
    {
        foreach (['category', 'brand', 'colour'] as $key) {
            if ($filters[$key]) {
                $qb->andWhere($qb->expr()->in('p.' . $key, '?' . $this->param));
                $qb->setParameter($this->param++, $filters[$key]);
            }
        }

        return $qb;
    }
}
