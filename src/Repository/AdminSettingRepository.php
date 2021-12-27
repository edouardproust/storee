<?php

namespace App\Repository;

use App\Entity\AdminSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminSetting[]    findAll()
 * @method AdminSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminSetting::class);
    }

    /**
     * Get AdminSetting object, based on slug
     * @var string $slug The slug of the setting
     * @return AdminSetting|null Returns anAdminSetting object or null
     */
    public function getOne(string $slug): ?AdminSetting
    {
        return $this->createQueryBuilder('settings')
            ->andWhere('settings.slug = :val')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
