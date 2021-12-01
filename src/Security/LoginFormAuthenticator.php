<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const LOGIN_ROUTE = 'security_login';
    private const SUCCESS_REDIRECT_ROUTE = 'user_show';
    private const SUCCESS_REDIRECT_ROUTE_ADMIN = 'admin';

    private $urlGenerator;
    private $flashBag;
    private $successRedirectUrl;
    private $successRedirectUrlAdmin;

    public function __construct(UrlGeneratorInterface $urlGenerator, FlashBagInterface $flashBag)
    {
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->successRedirectUrl = $urlGenerator->generate(self::SUCCESS_REDIRECT_ROUTE);
        $this->successRedirectUrlAdmin = $urlGenerator->generate(self::SUCCESS_REDIRECT_ROUTE_ADMIN);
    }

    public function supports(Request $request): ?bool
    {
        return $request->get("_route") === "security_login" && $request->getMethod() === "POST";
    }

    public function authenticate(Request $request): PassportInterface
    {    
        // retrieve credentials
        $email = $request->get('login')['email'];
        $password = $request->get('login')['password'];
        // Save username in case of error (to fill the field)
        $request->getSession()->set(Security::LAST_USERNAME, $email);
        // return
        return new Passport(
            new UserBadge($email), 
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if($sessionReferer = $request->getSession()->get('referer')) {
            $redirect = $sessionReferer;
        } elseif($queryReferer = $request->query->get('referer')) {
            $redirect = $queryReferer;
        } else {
            if(in_array("ROLE_ADMIN", $token->getUser()->getRoles())) {
                $redirect = $this->successRedirectUrlAdmin;
            } else {
                $redirect = $this->successRedirectUrl;
            }
        }
        // $this->flashBag->add('success', 'You are now connected');
        return new RedirectResponse($redirect);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return null; // defined in security.yaml under the 'logout' key
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $request->getSession()->set('referer', $request->headers->get('referer'));
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $authException);
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

}
