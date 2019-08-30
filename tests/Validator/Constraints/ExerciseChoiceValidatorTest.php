<?php

namespace App\Tests\Validator\Constraints;

use App\DTO\Exercise\Exercise;
use App\Repository\ExerciseRepository;
use App\Validator\Constraints\ExerciseChoice;
use App\Validator\Constraints\ExerciseChoiceValidator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ExerciseChoiceValidatorTest extends TestCase
{
    /**
     * @var ExerciseRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private ExerciseRepository $exerciseRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ChoiceValidator
     */
    private ChoiceValidator $choiceValidator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ExecutionContextInterface
     */
    private ExecutionContextInterface $executionContext;

    protected function setUp(): void
    {
        $this->exerciseRepository = $this->createMock(ExerciseRepository::class);
        $this->choiceValidator = $this->createMock(ChoiceValidator::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->exerciseRepository, $this->choiceValidator, $this->executionContext);
    }

    public function testValidate(): void
    {
        $object = new class {};

        $constraint = new ExerciseChoice();

        $id = Uuid::uuid4();
        $exercise = new Exercise($id, '', []);

        $expectedChoices = [
            $id->toString()
        ];

        $this->exerciseRepository
            ->expects(static::once())
            ->method('getExercisesList')
            ->willReturn([$exercise]);

        $this->choiceValidator
            ->expects(static::once())
            ->method('initialize')
            ->with($this->executionContext);

        $this->choiceValidator
            ->expects(static::once())
            ->method('validate')
            ->with($object, $constraint);

        $validator = new ExerciseChoiceValidator($this->exerciseRepository, $this->choiceValidator);
        $validator->initialize($this->executionContext);
        $validator->validate($object, $constraint);

        static::assertEquals($expectedChoices, $constraint->choices);
    }
}
