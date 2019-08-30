<?php

namespace App\Tests\Validator\Constraints;

use App\DTO\Exercise\Exercise;
use App\Repository\ExerciseRepository;
use App\Validator\Constraints\RecordValues;
use App\Validator\Constraints\RecordValuesValidator;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RecordValuesValidatorTest extends TestCase
{
    /**
     * @var ExerciseRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private ExerciseRepository $exerciseRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Assert\CollectionValidator
     */
    private Assert\CollectionValidator $collectionValidator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ExecutionContextInterface
     */
    private ExecutionContextInterface $executionContext;

    protected function setUp(): void
    {
        $this->exerciseRepository = $this->createMock(ExerciseRepository::class);
        $this->collectionValidator = $this->createMock(Assert\CollectionValidator::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->exerciseRepository, $this->collectionValidator, $this->executionContext);
    }

    public function testValidateInvalidConstraintThrowsException(): void
    {
        $constraint = new class extends Constraint {};

        $this->expectException(UnexpectedTypeException::class);

        $validator = new RecordValuesValidator($this->exerciseRepository, $this->collectionValidator);
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

        $validator = new RecordValuesValidator($this->exerciseRepository, $this->collectionValidator);
        $validator->initialize($this->executionContext);

        $this->expectException(NoSuchPropertyException::class);

        $validator->validate($object, $constraint);
    }

    public function testSkipValidationIfNoExerciseAvailable(): void
    {
        $exerciseId = 'test';

        $constraint = new RecordValues();
        $object = new class {
            public string $exercise;
        };
        $object->exercise = $exerciseId;

        $this->executionContext
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object);

        $this->exerciseRepository
            ->expects(static::once())
            ->method('getExerciseById')
            ->with($exerciseId)
            ->willThrowException(new NoResultException());

        $this->collectionValidator
            ->expects(static::never())
            ->method('validate');

        $validator = new RecordValuesValidator($this->exerciseRepository, $this->collectionValidator);
        $validator->initialize($this->executionContext);

        $validator->validate($object, $constraint);
    }

    public function testPassValidationToCollectionValidator(): void
    {
        $exerciseId = Uuid::uuid4();
        $attributes = ['rep', 'time'];
        $exercise = new Exercise($exerciseId, '', $attributes);

        $constraint = new RecordValues();
        $object = new class {
            public string $exercise;
        };
        $object->exercise = $exerciseId;

        $this->executionContext
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object);

        $this->exerciseRepository
            ->expects(static::once())
            ->method('getExerciseById')
            ->with($exerciseId)
            ->willReturn($exercise);

        $this->collectionValidator
            ->expects(static::once())
            ->method('initialize')
            ->with($this->executionContext);

        $this->collectionValidator
            ->expects(static::once())
            ->method('validate')
            ->with(
                $object,
                static::isInstanceOf(Assert\Collection::class)
            );

        $validator = new RecordValuesValidator($this->exerciseRepository, $this->collectionValidator);
        $validator->initialize($this->executionContext);

        $validator->validate($object, $constraint);
    }
}
