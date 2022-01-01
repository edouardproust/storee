<?php

namespace App\Controller;

use App\Form\UserType;
use App\App\Service\CheckoutService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $hasher, 
        PurchaseRepository $purchaseRepository,
        CheckoutService $checkoutService)
    {
        $this->em = $em;
        $this->hasher = $hasher;
        $this->purchaseRepository = $purchaseRepository;
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
        if($form->isSubmitted()) {
            if($form->isValid()) {
                if(!$form->get('password')->getData()) {
                    $user->setPassword($existingPassword);
                } else {
                    $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
                }
                $this->em->persist($user);
                $this->em->flush();
                $this->addFlash('success', 'Your informations have been updated successfully.');
            } else {
                $this->addFlash('danger', 'User not updated. Please correct errors in the form below.');
            }
        }
        // View
        return $this->render('account/show.html.twig', [
            'purchases' => $purchases,
            'userForm' => $form->createView(),
            'checkoutService' => $this->checkoutService
        ]);
    }

}