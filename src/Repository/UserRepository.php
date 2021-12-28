<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Get a list of Users, based on several criterias like: the limit etc.
     * 
     * @param ?int $maxResults Limit the number of Users | null for unlimited amount. Default: null
     * @param string $orderBy Property to order by. This must be a property of the User entity. Eg. 'views', 'purchases', 'createdAt',...
     * @param string $order Must be 'ASC' or 'DESC'. Default: 'DESC'
     * @return User[] Array of User object to which we add a 'sales' property (User::sales)
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
