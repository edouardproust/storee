<?php 
namespace App\EventListener\Doctrine;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/** 
 * Used to add functionalities when these events are fired: Creation or Update on a Product object via Doctrine
 */
class UserListener
{

    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        $this->setCreatedAt($user);
    }

    private function setCreatedAt(User $user): void
    {
        if(!$user->getCreatedAt()) {
            $user->setCreatedAt(new \DateTime());
        }
    }

}