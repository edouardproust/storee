<?php

namespace App\Controller\Checkout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutValidationController extends AbstractController
{

    /**
     * @Route("/checkout", name="checkout")
     * @return Response 
     */
    public function show(): Response
    {
        /*
        Fields list: firstname / lastname / street / postcode / city / country / email / password / phone

        IF user not connected: show Connect button + email field + password field
        ELSE: hide them

        IF not connected AND password field filled
        - IF email doesn't exist in database: Create an account
        - ELSE: show a flash on Success page
        */

        return $this->render("checkout/one-page/show.html.twig");
    }

    /**
     * @Route("/checkout/order-successful", name="checkout_success")
     * @return Response 
     */
    public function success(): Response
    {
        return $this->render("checkout/success.html.twig");
    }

    /**
     * @Route("/checkout/stripe-processing", name="checkout_validation_stripe")
     * @IsGranted("ROLE_USER")
     * @return Response 
     */
    public function stripe(): Response
    {
        return $this->redirectToRoute("checkout_success");
    }

}