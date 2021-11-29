<?php

namespace App\Controller\Checkout;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OnePageCheckoutController extends AbstractController
{

    /**
     * @Route("/checkout", name="checkout")
     * @return Response 
     */
    public function show(): Response
    {
        return $this->render("checkout/one-page/show.html.twig");
    }

}