<?php
namespace App\Tests\MessageHandler\Exercise;

use App\DTO\Exercise\CreateExercise;
use App\Entity\Exercise;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Handler\Exercise\CreateExerciseHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateExerciseHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->validator, $this->em);
    }

    public function testCreateExerciseValidationFailed(): void
    {
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations
            ->method('count')
            ->willReturn(1);

        $data = new CreateExercise();

        $this->validator
            ->method('validate')
            ->with($data)
            ->willReturn($violations);

        $this->em
            ->expects(static::never())
            ->method('persist');

        $this->expectException(ValidationException::class);

        $handler = new CreateExerciseHandler($this->validator, $this->em);
        $handler->createExercise($data);
    }

    public function testCreateExercise(): void
    {
        $user = new User('', '', '');
        $userId = $user->getId();

        $data = new CreateExercise();
        $data->user = $userId;
        $data->name = '';
        $data->attributes = [];

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations
            ->method('count')
            ->willReturn(0);

        $this->validator
            ->method('validate')
            ->with($data)
            ->willReturn($violations);

        $this->em
            ->method('getReference')
            ->with(User::class, $userId)
            ->willReturn($user);

        $this->em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(Exercise::class));

        $this->em
            ->expects(static::once())
            ->method('flush');

        $handler = new CreateExerciseHandler($this->validator, $this->em);
        $handler->createExercise($data);
    }
}
