<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
    private const SUCCESS_REDIRECT_ROUTE = 'admin';
    private $userRepo;
    private $urlGenerator;

    public function __construct(UserRepository $userRepo, UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepo = $userRepo;
        $this->urlGenerator = $urlGenerator;
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
        return new RedirectResponse(self::SUCCESS_REDIRECT_ROUTE);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return null; // defined in security.yaml under the 'logout' key
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $authException);
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

}
