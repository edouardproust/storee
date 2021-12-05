<?php

namespace App\Repository;

use App\Entity\DeliveryCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeliveryCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryCountry[]    findAll()
 * @method DeliveryCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryCountry::class);
    }

    // /**
    //  * @return DeliveryCountry[] Returns an array of DeliveryCountry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeliveryCountry
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
