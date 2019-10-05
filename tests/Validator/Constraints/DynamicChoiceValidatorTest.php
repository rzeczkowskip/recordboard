<?php

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\AbstractDynamicChoiceValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DynamicChoiceValidatorTest extends TestCase
{
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
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->executionContext
            ->method('getValidator')
            ->willReturn($this->validator);
    }

    protected function tearDown(): void
    {
        unset($this->choiceValidator, $this->validator);
    }

    public function testValidateInvalidConstraintThrowsException(): void
    {
        $constraint = new class extends Constraint {};

        $this->expectException(UnexpectedTypeException::class);

        $validator = new DynamicChoiceValidator([]);
        $validator->validate(null, $constraint);
    }

    public function testValidateCallsChoiceValidator(): void
    {
        $object = new class {};
        $constraint = new class extends Choice {};
        $choices = ['test'];

        $this->validator
            ->expects(static::once())
            ->method('validate')
            ->with(
                $object,
                static::callback(function (Choice $choice) use ($constraint) {
                    return $choice instanceof Choice &&
                        get_object_vars($choice) === get_object_vars($constraint);
                }),
            );

        $validator = new DynamicChoiceValidator($choices);
        $validator->initialize($this->executionContext);
        $validator->validate($object, $constraint);
    }
}

class DynamicChoiceValidator extends AbstractDynamicChoiceValidator
{
    private array $choices;

    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    protected function getChoices($value, Choice $constraint): array
    {
        return $this->choices;
    }
}
