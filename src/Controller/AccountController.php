<?php

namespace App\Controller;

use App\Form\UserType;
use App\App\Service\AccountService;
use App\App\Service\CheckoutService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $hasher, 
        PurchaseRepository $purchaseRepository,
        AccountService $accountService,
        CheckoutService $checkoutService)
    {
        $this->em = $em;
        $this->hasher = $hasher;
        $this->purchaseRepository = $purchaseRepository;
        $this->accountService = $accountService;
        $this->checkoutService = $checkoutService;
    }

    /**
     * @Route("/account", name="account_show")
     */
    public function show(Request $request): Response
    {
        // Orders list
        $user = $this->getUser();
        $purchases = $this->purchaseRepository->findBy(["user" => $user], ['createdAt' => 'DESC']);

        // Account infos
        $form = $this->createForm(UserType::class, $user);
        $existingPassword = $user->getPassword();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($user->getPassword() == null) {
                $user->setPassword($existingPassword);
            } else {
                $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
            }
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Your informations have been updated successfully.');
        }

        // View
        return $this->render('account/show.html.twig', [
            'purchases' => $purchases,
            'userForm' => $form->createView(),
            'accountService' => $this->accountService,
            'checkoutService' => $this->checkoutService
        ]);
    }

    /**
     * @Route("/account/delete-order/{id}", name="account_delete_order")
     */
    public function deletePurchase($id): RedirectResponse
    {
        $purchase = $this->purchaseRepository->find($id);
        $this->em->remove($purchase);
        $this->em->flush();
        $this->addFlash('success', 'This order has been cancelled.');

        return $this->redirectToRoute('account_show');
    }

        /**
     * @Route("/account/edit-order/{id}", name="account_edit_order")
     */
    public function editOrder($id): RedirectResponse
    {
        return $this->redirectToRoute('checkout_confirmation', [
            'id' => $id
        ]);
    }
}