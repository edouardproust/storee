<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function cart(): Response
    {
        return $this->render('checkout/cart.html.twig', [
            'controller_name' => 'CheckoutController',
        ]);
    }
}