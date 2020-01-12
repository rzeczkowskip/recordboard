<?php

namespace App\Tests\Validator\Constraints;

use App\Model\Exercise\Exercise;
use App\Repository\ExerciseRepository;
use App\Validator\Constraints\ExerciseChoice;
use App\Validator\Constraints\ExerciseChoiceValidator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExerciseChoiceValidatorTest extends TestCase
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
        unset($this->exerciseRepository, $this->executionContext);
    }

    /**
     * @param UuidInterface|null $user
     *
     * @dataProvider userProvider
     */
    public function testValidate(?UuidInterface $user = null): void
    {
        $object = new class($user) {
            public ?UuidInterface $user;

            public function __construct(?UuidInterface $user = null)
            {
                $this->user = $user;
            }
        };
        $constraint = new ExerciseChoice(['user' => $user ? 'user' : null]);

        $id = Uuid::uuid4();
        $exercise = new Exercise($id, '', []);

        $expectedChoices = [
            $id->toString()
        ];

        $this->exerciseRepository
            ->expects(static::once())
            ->method('getExercisesList')
            ->with($user)
            ->willReturn([$exercise]);

        $this->validator
            ->expects(static::once())
            ->method('validate')
            ->with(
                $object,
                static::callback(function (Choice $choice) use ($expectedChoices) {
                    return $choice instanceof Choice &&
                        $choice->choices = $expectedChoices;
                }),
            );

        $validator = new ExerciseChoiceValidator($this->exerciseRepository);
        $validator->initialize($this->executionContext);
        $validator->validate($object, $constraint);

        static::assertEquals($expectedChoices, $constraint->choices);
    }

    public function userProvider(): \Generator
    {
        yield 'with user' => [Uuid::uuid4()];
        yield 'without user' => [null];
    }
}
