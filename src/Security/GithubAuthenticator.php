<?php

namespace App\Security;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $router;
    private $session;

    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router, SessionInterface $session)
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->session = $session;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'login_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGithubClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $this->saveAccessToken($credentials);

        return $userProvider->loadUserByUsername($this->getGithubClient()->fetchUserFromToken($credentials)->getId());
    }

    /**
     * @return OAuth2Client
     */
    private function getGithubClient()
    {
        return $this->clientRegistry->getClient('github');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param AccessToken $accessToken
     */
    public function saveAccessToken(AccessToken $accessToken) : void
    {
        $this->session->set('access_token', $accessToken->getToken());
    }
}