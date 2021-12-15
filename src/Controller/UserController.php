<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

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
