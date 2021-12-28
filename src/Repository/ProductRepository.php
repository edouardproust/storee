<?php

namespace App\Repository;

use App\App\Service\AdminSettingService;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, AdminSettingService $adminSettingService)
    {
        parent::__construct($registry, Product::class);
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * Get a list of products, based on several criterias like: the category, the limit and the order criteria.
     * 
     * @param Category|null $category Filter product by categry | NULL to get all products. Default: null
     * @param ?int $maxResults Limit the number of products | null for unlimited amount. Default: null
     * @param string $orderBy Property to order by. This must be a property of the Product entity. Eg. 'views', 'purchases', 'createdAt',...
     * @param string $order Must be 'ASC' or 'DESC'. Default: 'DESC'
     * @return Product[] Array of Product object to which we add a 'sales' property (Product::sales)
     * @return array 
     */
    public function findForCollection(?Category $category = null, $maxResults, ?string $orderBy = null, ?string $order = null): array
    {
        $orderBy = $orderBy ?? $this->adminSettingService->getValue('collectionFilterDefault');
        $order = $order ?? 'desc';
        
        $query = $this->createQueryBuilder('p');
        if($category) $query->andWhere('p.category = '.$category->getId());
        if($orderBy) $query->orderBy('p.'.$orderBy, $order);
        return $query
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Product
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
