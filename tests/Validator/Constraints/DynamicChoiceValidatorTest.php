<?php

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\AbstractDynamicChoiceValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DynamicChoiceValidatorTest extends TestCase
{
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
        $this->choiceValidator = $this->createMock(ChoiceValidator::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->choiceValidator, $this->executionContext);
    }

    public function testValidateInvalidConstraintThrowsException(): void
    {
        $constraint = new class extends Constraint {};

        $this->expectException(UnexpectedTypeException::class);

        $validator = new DynamicChoiceValidator($this->choiceValidator);
        $validator->validate(null, $constraint);
    }

    public function testValidatePopulatesChoices(): void
    {
        $choices = ['test', 'value'];
        $constraint = new class extends Choice {};

        $validator = new DynamicChoiceValidator($this->choiceValidator, $choices);
        $validator->initialize($this->executionContext);
        $validator->validate(null, $constraint);

        static::assertEquals($choices, $constraint->choices);
    }

    public function testValidateCallsChoiceValidator(): void
    {
        $object = new class {};
        $constraint = new class extends Choice {};

        $this->choiceValidator
            ->expects(static::once())
            ->method('initialize')
            ->with($this->executionContext);

        $this->choiceValidator
            ->expects(static::once())
            ->method('validate')
            ->with($object, $constraint);

        $validator = new DynamicChoiceValidator($this->choiceValidator);
        $validator->initialize($this->executionContext);
        $validator->validate($object, $constraint);
    }
}

class DynamicChoiceValidator extends AbstractDynamicChoiceValidator
{
    private array $choices;

    public function __construct(?ChoiceValidator $choiceValidator = null, array $choices = ['test'])
    {
        parent::__construct($choiceValidator);
        $this->choices = $choices;
    }

    protected function getChoices($value, Choice $constraint): array
    {
        return $this->choices;
    }
}
