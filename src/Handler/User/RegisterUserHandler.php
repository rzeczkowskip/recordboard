<?php
namespace App\Handler\User;

use App\Entity\User;
use App\Exception\ValidationException;
use App\DTO\User\RegisterUser;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterUserHandler
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $this->em = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    public function register(RegisterUser $registerUser): User
    {
        if (($violations = $this->validator->validate($registerUser)) && $violations->count()) {
            throw new ValidationException($violations);
        }

        $authUser = new AuthUser(uuid_v4(), '');

        $password = $this->passwordEncoder->encodePassword($authUser, $registerUser->password);

        $user = new User(
            $registerUser->email,
            $password,
            $registerUser->name
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
