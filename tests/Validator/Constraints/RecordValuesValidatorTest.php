<?php

namespace App\Tests\Validator\Constraints;

use App\Entity\Exercise;
use App\Entity\User;
use App\Repository\ExerciseRepository;
use App\Validator\Constraints\RecordValues;
use App\Validator\Constraints\RecordValuesValidator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecordValuesValidatorTest extends TestCase
{
    /**
     * @var ExerciseRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private ExerciseRepository $exerciseRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ExecutionContextInterface
     */
    private ExecutionContextInterface $executionContext;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
     */
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->exerciseRepository = $this->createMock(ExerciseRepository::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->executionContext
            ->method('getValidator')
            ->willReturn($this->validator);
    }

    protected function tearDown(): void
    {
        unset($this->exerciseRepository, $this->collectionValidator, $this->executionContext);
    }

    public function testValidateInvalidConstraintThrowsException(): void
    {
        $constraint = new class extends Constraint {};

        $this->expectException(UnexpectedTypeException::class);

        $validator = new RecordValuesValidator($this->exerciseRepository);
        $validator->validate(null, $constraint);
    }

    public function testMissingExercisePropertyThrowsException(): void
    {
        $constraint = new RecordValues();
        $object = new class {};

        $this->executionContext
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object);

        $validator = new RecordValuesValidator($this->exerciseRepository);
        $validator->initialize($this->executionContext);

        $this->expectException(NoSuchPropertyException::class);

        $validator->validate($object, $constraint);
    }

    public function testSkipValidationIfNoExerciseAvailable(): void
    {
        $exerciseId = Uuid::uuid4();

        $constraint = new RecordValues();
        $object = new class {
            public UuidInterface $exercise;
        };
        $object->exercise = $exerciseId;

        $this->executionContext
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object);

        $this->exerciseRepository
            ->expects(static::once())
            ->method('findById')
            ->with($exerciseId)
            ->willReturn(null);

        $this->executionContext
            ->expects(static::never())
            ->method('getValidator');

        $validator = new RecordValuesValidator($this->exerciseRepository);
        $validator->initialize($this->executionContext);

        $this->expectException(UnexpectedTypeException::class);

        $validator->validate($object, $constraint);
    }

    public function testPassValidationToCollectionValidator(): void
    {
        $exerciseId = Uuid::uuid4();
        $attributes = ['rep', 'time'];
        $exercise = new Exercise(
            new User('', '', ''),
            '',
            $attributes);

        $constraint = new RecordValues();
        $object = new class {
            public UuidInterface $exercise;
        };
        $object->exercise = $exerciseId;

        $this->executionContext
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object);

        $this->exerciseRepository
            ->expects(static::once())
            ->method('findById')
            ->with($exerciseId)
            ->willReturn($exercise);

        $this->validator
            ->expects(static::once())
            ->method('validate')
            ->with(
                $object,
                static::isInstanceOf(Assert\Collection::class)
            );

        $validator = new RecordValuesValidator($this->exerciseRepository);
        $validator->initialize($this->executionContext);

        $validator->validate($object, $constraint);
    }
}
