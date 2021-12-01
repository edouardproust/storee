<?php

namespace App\Controller\Checkout;

use App\App\Cart\CartService;
use App\Form\Purchase\PurchaseOnePageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutOnePageController extends AbstractController
{

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/checkout", name="checkout_one_page")
     * @return Response 
     */
    public function show(Request $request): Response
    {
        /*
        Fields list: firstname / lastname / street / postcode / city / country / email / password / phone

        Check if user is connected
        - IF false: 
            show Connect button + email field + password field
        - ELSE: 
            hide Connect button + email field + password field
            fill the "delivery address" form with user data

        IF not connected AND password field filled
        - IF email doesn't exist in database: Create an account
        - ELSE: show a flash on Success page
        */

        $form = $this->createForm(PurchaseOnePageType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('checkout_validation');
        }
        return $this->render("checkout/one-page/show.html.twig", [
            'formCheckout' => $form->createView(),
            'cart' => $this->cartService->getDetailedCart(),
            'total' => $this->cartService->getTotal()
        ]);
    }

}