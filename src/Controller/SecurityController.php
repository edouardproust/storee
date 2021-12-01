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
    public function logout(Request $request): ?Response
    {
        return null;
    }

    /**
     * @Route("/login/logged-out", name="security_logout_handler")
     */
    public function logoutHandler(Request $request, AuthenticationUtils $utils): Response
    {
        $this->addFlash('success', 'Your are now logged out.');
        return $this->redirectToRoute('security_login');
    }  
}
