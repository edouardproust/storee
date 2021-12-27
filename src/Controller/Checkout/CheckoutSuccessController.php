<?php

namespace App\Controller\Checkout;

use App\App\Service\CheckoutService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutSuccessController extends AbstractController
{
    /** @var PurchaseRepository */
    private $purchaseRepository;

    /** @var EntityManagerInterface */
    private $em;

    /** @var CheckoutService */
    private $checkoutService;

    public function __construct(
        PurchaseRepository $purchaseRepository, 
        EntityManagerInterface $em, 
        CheckoutService $checkoutService
    ){
        $this->purchaseRepository = $purchaseRepository;
        $this->em = $em;
        $this->checkoutService = $checkoutService;
    }

    /** 
     * @Route("/checkout/{id}/success", name="checkout_success")
     * @return Response 
     */
    public function success($id, SessionInterface $session, EventDispatcherInterface $dispatcher): Response
    {
        $purchase = $this->purchaseRepository->find($id);
        if(!$this->checkoutService->accessGranted($purchase)) {
            return $this->redirectToRoute('cart');
        }

        // Update database
            // 1. Turn status on 'paid' [TODO: Use Stripe WebHooks to do this in a secure manner!]
            $purchase->setStatus(Purchase::STATUS_PAID);
            $this->em->persist($purchase);
            $this->em->flush();
            // 2. Add units sold to each products
            foreach($purchase->getPurchaseItems() as $item) {
                $product = $item->getProduct();
                $product->addPurchase($item->getQuantity());
                $this->em->persist($product);
            }
            $this->em->flush();

        // empty cart
        $session->remove('cart');

        // Event hook
        $purchaseSuccessEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseSuccessEvent, 'purchase.success');

        return $this->render("checkout/success.html.twig");
        
    }

}