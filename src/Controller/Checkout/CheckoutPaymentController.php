<?php 

namespace App\Controller\Checkout;

use App\App\Service\StripeService;
use App\Repository\UserRepository;
use App\App\Service\CheckoutService;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutPaymentController extends AbstractController
{
    /** @var CheckoutService */
    private $checkoutService;
    /** @var StripeService */
    private $stripeService;
    /** @var PurchaseRepository */
    private $purchaseRepository;

    public function __construct(CheckoutService $checkoutService, StripeService $stripeService, PurchaseRepository $purchaseRepository, UserRepository $userRepository)
    {
        $this->checkoutService = $checkoutService;
        $this->stripeService = $stripeService;
        $this->purchaseRepository = $purchaseRepository;
        $this->userRepository = $userRepository;
    }

    /** 
     * @Route("checkout/{id}/pay/{method}", name="checkout_payment")
     * @link https://stripe.com/docs/payments/quickstart
     * @return Response
    */
    public function payment($id, $method): Response
    {
        // check access
        $purchase = $this->purchaseRepository->findOneBy(['id' => $id]);
        if(!$this->checkoutService->accessGranted($purchase)) {
            return $this->redirectToRoute('cart');
        }
        // payment options & process
        $params = [];
        // - stripe
        if($method === 'stripe') {
            $publicKey = $this->stripeService->getPublicKey();
            $clientSecret = $this->stripeService->getClientSecret($purchase);
            $successRoute = 'checkout_success'; 
            $params = [
                'publicKey' => $publicKey,
                'clientSecret' => $clientSecret,
                'successRoute' => $successRoute,
                'purchase' => $purchase,
                'productsTotal' => $this->checkoutService->getTotalWithoutDelivery($purchase)
            ];
        }
        // redirect
        return $this->render("checkout/payment.html.twig", $params);
    }

}