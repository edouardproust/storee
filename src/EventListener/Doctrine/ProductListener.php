<?php 
namespace App\EventListener\Doctrine;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/** 
 * Used to add functionalities when these events are fired: Creation or Update on a Product object via Doctrine
 */
class ProductListener
{

    /** @var SluggerInterface */ 
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Product $product, LifecycleEventArgs $args): void
    {
        $this->setCreatedAt($product);
        $this->setSlug($product);
    }

    public function preUpdate(Product $product, LifecycleEventArgs $args): void
    {
        $this->setSlug($product);
    }

    private function setCreatedAt(Product $product): void
    {
        if(!$product->getCreatedAt()) {
            $product->setCreatedAt(new \DateTime());
        }
    }

    private function setSlug(Product $product): void
    {
        if(!$product->getSlug()) {
            $product->setSlug(strtolower($this->slugger->slug($product->getName())));
        }
    }

}