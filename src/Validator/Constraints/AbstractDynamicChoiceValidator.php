<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

abstract class AbstractDynamicChoiceValidator extends ConstraintValidator
{
    private ?ChoiceValidator $choiceValidator;

    public function __construct(?ChoiceValidator $choiceValidator = null)
    {
        $this->choiceValidator = $choiceValidator;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Choice) {
            throw new UnexpectedTypeException($constraint, Choice::class);
        }

        $constraint->choices = $this->getChoices($value, $constraint);

        $validator = $this->getChoiceValidator();
        $validator->initialize($this->context);
        $validator->validate(
            $this->getRealValue($value),
            $constraint
        );
    }

    /**
     * Allows to reformat value before sending it to choice validator eg. change entity class to scalar
     *
     * @param $value
     *
     * @return mixed
     */
    protected function getRealValue($value)
    {
        return $value;
    }

    private function getChoiceValidator(): ChoiceValidator
    {
        if (!isset($this->choiceValidator)) {
            $this->choiceValidator = new ChoiceValidator();
        }

        return $this->choiceValidator;
    }

    abstract protected function getChoices($value, Choice $constraint): array;
}
