<?php

namespace App\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint extends AbstractController implements AuthenticationEntryPointInterface
{

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        // // strore previous page in session storage (to redirect back to it after logged in)
        // $request->getSession()->set('referer', $request->headers->get('referer'));

        // flash a custom message wether the user requested the "admin" page or not
        $currentUrl = $request->server->get('REQUEST_URI');
        $adminUrl = $this->generateUrl('admin');
        if(strpos($currentUrl, $adminUrl) !== false) {
            $this->addFlash('danger', 'Only administrators are allowed to access this page.');
        } else {
            $this->addFlash('danger', 'You must log in to access this page.');
        }

        // redirect to login page
        return new RedirectResponse($this->generateUrl('security_login'));
    }
}