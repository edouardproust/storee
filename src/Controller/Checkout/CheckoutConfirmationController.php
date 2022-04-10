<?php

namespace App\Controller\Checkout;

use App\App\Service\CartService;
use App\App\Service\CheckoutService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DeliveryCountryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\PurchaseConfirmationType;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutConfirmationController extends AbstractController
{

    /** @var CheckoutService */
    private $checkoutService;
    /** @var CartService */
    private $cartService;

    public function __construct(
        PurchaseRepository $purchaseRepository,
        CartService $cartService,
        CheckoutService $checkoutService,
        DeliveryCountryRepository $deliveryCountriesRepo,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
        $this->deliveryCountriesRepo = $deliveryCountriesRepo;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/checkout/{id?0}", name="checkout_confirmation")
     * @return Response 
     */
    public function confirmation($id, Request $request): Response
    {
        // redirect
        $purchase = $this->purchaseRepository->find($id);
        if ($purchase) {
            if (!$this->checkoutService->accessGranted($purchase)) {
                return $this->redirectToRoute('cart');
            }
        } else {
            // If no purchase yet: create a new one
            $purchase = $this->checkoutService->createPurchase();
        }
        // display form
        $form = $this->createForm(PurchaseConfirmationType::class, $purchase);
        // form request handle
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $purchase = $form->getData();
            // save user account (if not logged in and 'password' field is not empty)
            $user = $this->getUser();
            if (!$user && $purchase->getPassword()) {
                $user = $this->checkoutService->createUser($purchase);
                $this->em->persist($user);
                $this->em->flush();
                // add user to purchase (user id is now generated)
                $this->addFlash('success', 'An account has been created with your email ' . $user->getEmail() . ' and the password you provided.');
            }
            // save purchase
            $this->checkoutService->updatePurchase($purchase, $user);
            $this->em->persist($purchase);
            foreach ($purchase->getPurchaseItems() as $item) {
                $this->em->persist($item);
            }
            $this->em->persist($purchase);
            $this->em->flush();
            // redirect to payment
            return $this->redirectToRoute('checkout_payment', [
                'id' => $purchase->getId(),
                'method' => $purchase->getPaymentMethod()->getSlug()
            ]);
        }

        return $this->render("checkout/confirmation.html.twig", [
            'formCheckout' => $form->createView(),
            'loginLink' => $this->checkoutService->createLinkWithReferer('security_login', $request->get("_route")),
            'cart' => $purchase->getPurchaseItems(),
            'total' => $purchase->getTotal(),
        ]);
    }
}
