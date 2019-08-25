<?php
namespace App\Tests\MessageHandler\User;

use App\Entity\User;
use App\Message\User\RegisterUser;
use App\MessageHandler\User\RegisterUserHandler;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterUserHandlerTest extends TestCase
{
    public function testRegisterUser(): void
    {
        $email = 'admin@example.com';
        $password = 'secret123';
        $name = 'test';
        $encodedPassword = 'encodedsecret123';

        $em = $this->createMock(EntityManagerInterface::class);
        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $message = RegisterUser::withData($email, $password, $name);

        $passwordEncoder
            ->expects(static::once())
            ->method('encodePassword')
            ->with(static::isInstanceOf(AuthUser::class), $password)
            ->willReturn($encodedPassword);

        $em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(User::class));

        $em
            ->expects(static::once())
            ->method('flush');

        $handler = new RegisterUserHandler($em, $passwordEncoder);
        $handler($message);
    }
}
