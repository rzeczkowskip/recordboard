<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    public const AUTH_TOKEN_HEADER = 'Authorization';

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::AUTH_TOKEN_HEADER);
    }

    public function getCredentials(Request $request): string
    {
        return substr($request->headers->get(self::AUTH_TOKEN_HEADER), 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        try {
            $user = $this->userRepository->getUserByApiToken($credentials);

            return $userProvider->loadUserByUsername($user->getEmail());
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): void
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): void
    {
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('', Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
