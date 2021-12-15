<?php 
namespace App\EventListener\Doctrine;

use App\Entity\PaymentMethod;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/** 
 * Used to add functionalities when these events are fired: Creation or Update on a Product object via Doctrine
 */
class PaymentMethodListener
{

    /** @var SluggerInterface */ 
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(PaymentMethod $paymentMethod, LifecycleEventArgs $args): void
    {
        $this->setSlug($paymentMethod);
    }

    public function preUpdate(PaymentMethod $paymentMethod, LifecycleEventArgs $args): void
    {
        $this->setSlug($paymentMethod);
    }

    private function setSlug(PaymentMethod $paymentMethod): void
    {
        if(!$paymentMethod->getSlug()) {
            $paymentMethod->setSlug(strtolower($this->slugger->slug($paymentMethod->getName())));
        }
    }

}