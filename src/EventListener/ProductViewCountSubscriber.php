<?php

namespace App\EventListener;

use App\Event\ProductViewEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductViewCountSubscriber implements EventSubscriberInterface
{

    /** @var TokenStorageInterface  */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'countViews'
        ];
    }

    /**
     * Update productViewCount log file each time a product is visited
     * @param ProductViewEvent $productViewEvent 
     * @return void 
     */
    public function countViews(ProductViewEvent $productViewEvent): void
    {
        // Prevent adding views if the user is Admin
        $token = $this->tokenStorage->getToken();
        if($token && in_array('ROLE_ADMIN', $token->getUser()->getRoles())) {
            return;
        }
        $product = $productViewEvent->getProduct();
        $product->setViews($product->getViews() + 1);
        $this->em->persist($product);
        $this->em->flush();
    }

}