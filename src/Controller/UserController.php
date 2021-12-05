<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\App\Service\AccountService;
use App\App\Service\CheckoutService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/account", name="user_show")
     */
    public function show(
        Request $request, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $hasher, 
        PurchaseRepository $purchaseRepository,
        AccountService $accountService,
        CheckoutService $checkoutService
    ): Response
    {
        // Orders list
        $user = $this->getUser();
        $purchases = $purchaseRepository->findBy(["user" => $user], ['createdAt' => 'DESC']);

        // Account infos
        $form = $this->createForm(UserType::class, $user);
        $existingPassword = $user->getPassword();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($user->getPassword() == null) {
                $user->setPassword($existingPassword);
            } else {
                $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            }
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your informations have been updated successfully.');
        }

        // View
        return $this->render('user/show.html.twig', [
            'purchases' => $purchases,
            'userForm' => $form->createView(),
            'accountService' => $accountService,
            'checkoutService' => $checkoutService
        ]);
    }

    /**
     * @Route("/register", name="user_create")
     */
    public function create(): Response
    {
        return $this->render('user/create.html.twig');
    }

        /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function delete($id, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $userRepo->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute("admin_products");
        return $this->redirectToRoute("home");
    }

}
