<?php

namespace App\Controller\Checkout;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutSuccessController extends AbstractController
{
    /** @var PurchaseRepository */
    private $purchaseRepository;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(PurchaseRepository $purchaseRepository, EntityManagerInterface $em)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->em = $em;
    }

    /** 
     * @Route("/checkout/{id}/success", name="checkout_success")
     * @return Response 
     */
    public function success($id, SessionInterface $session): Response
    {
        $purchase = $this->purchaseRepository->find($id);

        if(
            $purchase !== null &&
            $this->getUser() === $purchase->getUser() &&
            $purchase->getStatus() === Purchase::STATUS_PAID
        ) { 
            // Turn status on 'paid' 
            // [TODO: Use Stripe WebHooks to do this in a secure manner!]
            $purchase->setStatus(Purchase::STATUS_PAID);
            $this->em->persist($purchase);
            $this->em->flush();
        
            // empty cart
            $session->remove('cart');

            return $this->render("checkout/success.html.twig");

        } else {
            // redirect
            return $this->redirectToRoute("cart");
        }
        
    }

}