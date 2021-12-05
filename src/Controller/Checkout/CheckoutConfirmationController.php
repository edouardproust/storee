<?php

namespace App\Controller\Checkout;

use App\App\Service\CartService;
use App\App\Service\CheckoutService;
use App\Form\Purchase\PurchaseOnePageType;
use App\Repository\DeliveryCountryRepository;
use App\App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutConfirmationController extends AbstractController
{

    public function __construct(
        CartService $cartService, 
        CheckoutService $checkoutService, 
        StripeService $stripeService,
        DeliveryCountryRepository $deliveryCountriesRepo,
        EntityManagerInterface $em
    ){
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
        $this->stripeService = $stripeService;
        $this->deliveryCountriesRepo = $deliveryCountriesRepo;
        $this->em = $em;

    }

    /**
     * @Route("/checkout", name="checkout_confirmation")
     * @return Response 
     */
    public function confirmation(Request $request, SessionInterface $session): Response
    {
        // VERIFIER SI STATUT DE LA COMMAND EST "PENDING"
        // -> sinon rediriger soit vers les commandes, soit vers le page d'accueil

        // form display
        $data = $this->checkoutService->getUserData();
        $form = $this->createForm(PurchaseOnePageType::class, $data);
        // form request handle
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if($form->isValid()) {
                // build purchase
                $formData = $form->getData();
                $formData['total'] = $this->cartService->getTotal();
                $purchase = $this->checkoutService->setPurchase($formData);
                // save to database
                $this->em->persist($purchase);
                foreach($purchase->getPurchaseItems() as $item) {
                    $this->em->persist($item);
                }
                $this->em->flush();
                // redirect to payment
                $this->redirectToRoute('checkout_payment', ['method' => $purchase->getPaymentMethod()]);
            } else {
                $this->addFlash('danger', 'The informations you submitted are not valid. Please correct them below.');
            }
        }
        
        return $this->render("checkout/confirmation.html.twig", [
            'formCheckout' => $form->createView(),
            'loginLink' => $this->checkoutService->createLinkWithReferer('security_login', $request->get("_route")),
            'cart' => $this->cartService->getDetailedCart(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

}