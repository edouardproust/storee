<?php

namespace App\Security;

use PasswordHash;
use App\Entity\User;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class CheckoutRegisterAuthenticator extends AbstractAuthenticator
{

    private $purchaseRepository;

    public function __construct(PurchaseRepository $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }
    
    public function supports(Request $request): ?bool
    {
        return $request->get("_route") === "checkout_payment" && $this->getUser($request);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $this->getUser($request)->getEmail();
        return new SelfValidatingPassport(new UserBadge($email));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    public function getUser(Request $request): ?User
    {
        $purchaseId = $request->get('_route_params')['id'] ?? null;
        if(!$purchaseId) return null;
        $purchase = $this->purchaseRepository->find($purchaseId);
        if($purchase) return $purchase->getUser();
        return null;
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
