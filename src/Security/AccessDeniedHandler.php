<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler extends AbstractController implements AccessDeniedHandlerInterface
{

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // if is connected: send to "Access denied" page
        return new RedirectResponse($this->generateUrl('access_denied'));
    }
}
