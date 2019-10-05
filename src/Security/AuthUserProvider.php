<?php
namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthUserProvider implements UserProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername($username): AuthUser
    {
        try {
            $user = $this->userRepository->getAuthData($username);

            return new AuthUser($user['id'], $user['password']);
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf(
                'User %s not found',
                $username
            ));
        }
    }

    public function refreshUser(UserInterface $user): AuthUser
    {
        if (!$user instanceof AuthUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return $class === AuthUser::class;
    }
}
