<?php 

namespace App\Controller\Checkout;

use App\App\Service\StripeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutPaymentController extends AbstractController
{

    /** 
     * @Route("checkout/{id}/payment/stripe", name="checkout_payment_stripe")
     * @link https://stripe.com/docs/payments/quickstart
     * @return Response
    */
    public function stripe($id, StripeService $stripeService): Response
    {
        $clientSecret = $stripeService->getClientSecret();
        $successRoute = 'checkout_success';
            
        return $this->render("checkout/payment-stripe.html.twig", [
            'clientSecret' => $clientSecret,
            'successRoute' => $successRoute
        ]);
    }

}