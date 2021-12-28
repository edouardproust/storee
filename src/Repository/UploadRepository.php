<?php

namespace App\Repository;

use App\Entity\Upload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Upload|null find($id, $lockMode = null, $lockVersion = null)
 * @method Upload|null findOneBy(array $criteria, array $orderBy = null)
 * @method Upload[]    findAll()
 * @method Upload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Upload::class);
    }

    /**
     * Get a list of uploads, based on several criterias like: the category, the limit and the order criteria.
     * 
     * @param ?int $maxResults Limit the number of Uploads | null for unlimited amount. Default: null
     * @param string $orderBy Property to order by. This must be a property of the Upload entity. Eg. 'views', 'purchases', 'createdAt',...
     * @param string $order Must be 'ASC' or 'DESC'. Default: 'DESC'
     * @return Upload[] Array of Upload object to which we add a 'sales' property (Upload::sales)
     * @return array 
     */
    public function findForCollection($maxResults = null, ?string $orderBy = null, ?string $order): array
    {        
        $orderBy = $orderBy ?? 'createdAt';
        $order = $order ?? 'desc';

        $query = $this->createQueryBuilder('p');
        if($orderBy) $query->orderBy('p.'.$orderBy, $order);
        return $query
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

}
