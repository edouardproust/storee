<?php 
namespace App\EventListener\Doctrine;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\Event\PreFlushEventArgs;
use App\Repository\PurchaseItemRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/** 
 * Used to add functionalities when these events are fired: Creation or Update on a Purchase object via Doctrine
 */
class PurchaseListener
{

    /** @var PurchaseItemRepository */
    private $purchaseItemRepository;

    public function __construct(PurchaseItemRepository $purchaseItemRepository)
    {
        $this->purchaseItemRepository = $purchaseItemRepository;
    }

    public function prePersist(Purchase $purchase, LifecycleEventArgs $args): void
    {
        $this->setCreatedAt($purchase);
    }

    public function preFlush(Purchase $purchase, PreFlushEventArgs $args): void
    {
        $this->setTotal($purchase);
    }

    private function setTotal(Purchase $purchase): void
    {
        if(!$purchase->getTotal()) {
            /** @var PurchaseItem */
            $purchaseItems = $purchase->getPurchaseItems();
            if(empty($purchaseItems)) {
                $purchaseItems = $this->purchaseItemRepository->findBy(['purchase' => $purchase]);
            }
            $productsTotal = 0;
            foreach($purchaseItems as $item) {
                $productsTotal += $item->getTotal();
            }
            $totalWithDelivery = $productsTotal + $purchase->getDeliveryMethod()->getPrice();
            $purchase->setTotal($totalWithDelivery);
        }
    }

    private function setCreatedAt(Purchase $purchase): void
    {
        if(!$purchase->getCreatedAt()) {
            $purchase->setCreatedAt(new \DateTime());
        }
    }

}