<?php

namespace Ladamalina\RemoteUserBundle\Security\Guard;

use Ladamalina\RemoteUserBundle\Security\AbstractRemoteUserProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class Authenticator extends AbstractGuardAuthenticator
{
    /**
     * @param Request $request
     * @return AuthenticationCredentials|null
     */
    public function getCredentials(Request $request) {
        if (!$request->isMethod('POST')) {
            return null;
        }

        $username = $request->get('rua_username');
        $password = $request->get('rua_password');
        if (
            $username === null
            or $password === null
        ) {
            return null;
        }

        return new AuthenticationCredentials($username, $password);
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        /** @var AbstractRemoteUserProvider $userProvider */
        /** @var AuthenticationCredentials $credentials */
        $user = $userProvider->loadUserByUsernameAndPassword(
            $credentials->getUsername(),
            $credentials->getPassword()
        );

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(null, 403);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(null, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
