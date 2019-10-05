<?php
namespace App\Tests\MessageHandler\User;

use App\DTO\User\RegisterUser;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Handler\User\RegisterUserHandler;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterUserHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private EntityManagerInterface $em;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->validator, $this->em, $this->passwordEncoder);
    }

    public function testRegisterUserFailValidation(): void
    {
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations
            ->method('count')
            ->willReturn(1);

        $data = new RegisterUser();

        $this->validator
            ->method('validate')
            ->with($data)
            ->willReturn($violations);

        $this->em
            ->expects(static::never())
            ->method('persist');

        $this->expectException(ValidationException::class);

        $handler = new RegisterUserHandler($this->em, $this->passwordEncoder, $this->validator);
        $handler->register($data);
    }

    public function testRegisterUser(): void
    {
        $data = new RegisterUser();
        $data->email = 'test@example.com';
        $data->name = 'John Doe';
        $data->password = 'password';

        $hashedPassword = 'secret123';

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations
            ->method('count')
            ->willReturn(0);

        $this->passwordEncoder
            ->expects(static::once())
            ->method('encodePassword')
            ->with(static::isInstanceOf(AuthUser::class), $data->password)
            ->willReturn($hashedPassword);

        $this->em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(User::class));

        $this->em
            ->expects(static::once())
            ->method('flush');

        $handler = new RegisterUserHandler($this->em, $this->passwordEncoder, $this->validator);
        $handler->register($data);
    }
}
