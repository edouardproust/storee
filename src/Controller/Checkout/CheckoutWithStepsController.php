<?php 

namespace App\Controller\Checkout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutWithStepsController extends AbstractController
{

    /**
     * @Route("/checkout/client-informations", name="checkout_userdata")
     * @return Response 
     */
    public function clientInformations(): Response
    {
        $formUserData = $this->createForm(PurchaseUserDataType::class);

        return $this->render("checkout/one-page/show.html.twig", [
            'formUserData' => $formUserData->createView()
        ]);
    }

    /**
     * @Route("/checkout/delivery", name="checkout_delivery")
     * @return Response 
     */
    public function delivery(): Response
    {
        $formDelivery = $this->createForm(PurchaseDeliveryType::class);

        return $this->render("checkout/one-page/show.html.twig", [
            'formDelivery' => $formDelivery->createView()
        ]);
    }

    /**
     * @Route("/checkout/payment-stripe", name="checkout_payment_stripe")
     * @return Response 
     */
    public function paymentStripe(): Response
    {
        return $this->render('');
    }

}