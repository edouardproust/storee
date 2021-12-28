<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Get a list of Categories, based on several criterias like: the limit etc.
     * 
     * @param ?int $maxResults Limit the number of Categories | null for unlimited amount. Default: null
     * @param string $orderBy Property to order by. This must be a property of the Category entity. Eg. 'views', 'purchases', 'createdAt',...
     * @param string $order Must be 'ASC' or 'DESC'. Default: 'DESC'
     * @return Category[] Array of Category object to which we add a 'sales' property (Category::sales)
     * @return array 
     */
    public function findForCollection($maxResults = null, ?string $orderBy = null, ?string $order = null): array
    {        
        $orderBy = $orderBy ?? 'name';
        $order = $order ?? 'asc';

        $query = $this->createQueryBuilder('p');
        if($orderBy) $query->orderBy('p.'.$orderBy, $order);
        return $query
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }
}
