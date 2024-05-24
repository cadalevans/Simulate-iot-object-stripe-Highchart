<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private RequestStack $requestStack;

    public function __construct(private UrlGeneratorInterface $urlGenerator,RequestStack $requestStack)
    {
        $this->requestStack=$requestStack;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Get the authenticated user
        $user = $token->getUser();
        // Get the session from the request stack
        $session = $this->requestStack->getSession();

        // Store the user ID in the session
        $session->set('user_id', $user->getId());


        // Check if the user object is an instance of User class
        if ($user instanceof User) {

            // Get the request object from the request stack
            $request = $this->requestStack->getCurrentRequest();

            // Forward the current user object to the ModuleController
            $request->attributes->set('_user', $user);
        }

        //if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        //     return new RedirectResponse($targetPath);
        // }

        // Check if the user has the ROLE_ADMIN role
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // Redirect to the dashboard route for admins

            return new RedirectResponse($this->urlGenerator->generate('index'));
        } else {
            // Redirect to the main route for regular users
            return new RedirectResponse($this->urlGenerator->generate('app_module'));
        }

        // For example:
         //return new RedirectResponse($this->urlGenerator->generate('app_home'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
