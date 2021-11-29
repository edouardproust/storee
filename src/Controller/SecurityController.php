<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(Request $request, AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);
        return $this->render('security/login.html.twig', [
            'loginForm' =>$form->createView(),
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): ?Response
    {
        return null;
    }
}
