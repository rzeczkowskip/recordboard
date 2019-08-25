<?php
namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\RegisterUser;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterUserHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(RegisterUser $registerUser): void

    {
        $authUser = new AuthUser(Uuid::uuid4(), '', '');

        $password = $this->passwordEncoder->encodePassword($authUser, $registerUser->password);

        $user = new User(
            $registerUser->email,
            $password,
            $registerUser->name
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}
