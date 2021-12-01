<?php

namespace App\Controller\Checkout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutValidationController extends AbstractController
{

    /**
     * @Route("/checkout/stripe-processing", name="checkout_validation")
     * @return Response 
     */
    public function validation(SessionInterface $session): Response
    {
        // Autorize access to 'thank you' page
        $session->set('checkout_success', 1);
        return $this->redirectToRoute("checkout_success");
    }

    /** 
     * @Route("/checkout/success", name="checkout_success")
     * @return Response 
     */
    public function success(SessionInterface $session): Response
    {
        // check if authorization to access has been granted by the 'validation' method
        if($session->get('checkout_success')) {
            // authorize
            return $this->render("checkout/success.html.twig");
        } else {
            // redirect
            return $this->redirectToRoute("home");
        }
        
    }

}