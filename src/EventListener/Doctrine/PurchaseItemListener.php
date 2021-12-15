<?php 
namespace App\EventListener\Doctrine;

use App\Entity\PurchaseItem;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/** 
 * Used to add functionalities when these events are fired: Creation or Update on a Purchase object via Doctrine
 */
class PurchaseItemListener
{

    public function prePersist(PurchaseItem $item, LifecycleEventArgs $args): void
    {
        $this->setTotal($item);
        $this->setProductData($item);
    }

    private function setTotal(PurchaseItem $item): void
    {
        if(!$item->getTotal()) {
            $item->setTotal($item->getProduct()->getPrice() * $item->getQuantity());
        }
    }

    private function setProductData(PurchaseItem $item): void
    {
        if(empty($item->getProductData())) {
            $item->setProductData(serialize($item->getProduct()));
        }
    }

}