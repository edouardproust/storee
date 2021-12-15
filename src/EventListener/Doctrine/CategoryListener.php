<?php 
namespace App\EventListener\Doctrine;

use App\Entity\Category;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/** 
 * Used to add functionalities when these events are fired: Creation or Update on a Product object via Doctrine
 */
class CategoryListener
{
    
    /** @var SluggerInterface */ 
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Category $category, LifecycleEventArgs $args): void
    {
        $this->setSlug($category);
    }

    public function preUpdate(Category $category, LifecycleEventArgs $args): void
    {
        $this->setSlug($category);
    }

    private function setSlug(Category $category): void
    {
        if(!$category->getSlug()) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
        }
    }

}