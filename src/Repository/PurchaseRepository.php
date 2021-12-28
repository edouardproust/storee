<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    /**
     * Get a list of purchases, based on several criterias like: the category, the limit and the order criteria.
     * 
     * @param ?int $maxResults Limit the number of Purchases | null for unlimited amount. Default: null
     * @param string $orderBy Property to order by. This must be a property of the Purchase entity. Eg. 'views', 'purchases', 'createdAt',...
     * @param string $order Must be 'ASC' or 'DESC'. Default: 'DESC'
     * @return Purchase[] Array of Purchase object to which we add a 'sales' property (Purchase::sales)
     * @return array 
     */
    public function findForCollection($maxResults = null, ?string $orderBy, ?string $order): array
    {         
        $orderBy = $orderBy ?? 'id';
        $order = $order ?? 'desc';
        
        $query = $this->createQueryBuilder('p');
        if($orderBy) $query->orderBy('p.'.$orderBy, $order);
        return $query
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

}
