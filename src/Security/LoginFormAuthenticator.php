<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractAuthenticator
 {
  private $userRepository;
  private $urlGenerator;

  public function __construct(UserRepository $userRepository,UrlGeneratorInterface $urlGenerator)
  {
      $this->userRepository=$userRepository;
      $this->urlGenerator=$urlGenerator;
  }
     
      /**
     * Does the authenticator support the given Request?
     *
     * If this returns true, authenticate() will be called. If false, the authenticator will be skipped.
     *
     * Returning null means authenticate() can be called lazily when accessing the token storage.
     */
    public function supports(Request $request): ?bool
    {
      
        return $request->attributes->get('_route')==='app_login'
        && $request->isMethod('POST');

    }

    /**
     * Create a passport for the current request.
     *
     * The passport contains the user, credentials and any additional information
     * that has to be checked by the Symfony Security system. For example, a login
     * form authenticator will probably return a passport containing the user, the
     * presented password and the CSRF token value.
     *
     * You may throw any AuthenticationException in this method in case of error (e.g.
     * a UserNotFoundException when the user cannot be found).
     *
     * @throws AuthenticationException
     *
     * @return Passport
     */
    public function authenticate(Request $request)
    {
      $user=$this->userRepository->findOneByUsername($request->request->get('username'));
       
             
      if(! $user){
        throw new CustomUserMessageAuthenticationException("User doesn't exist");

      }

      return new Passport(
        new UserBadge($request->request->get('username')),
        new PasswordCredentials($request->request->get('password')),
        [new CsrfTokenBadge('login_form', $request->request->get('csrf_token')),
         new RememberMeBadge]);
    } /*: Passport;*/

    
    
    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
      $session= new Session();
      $session->getFlashBag()->add('warning',"Mauvais mot de passe ou nom d utilisateur");

      return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    


 }