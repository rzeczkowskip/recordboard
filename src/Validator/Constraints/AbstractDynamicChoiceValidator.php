<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

abstract class AbstractDynamicChoiceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Choice) {
            throw new UnexpectedTypeException($constraint, Choice::class);
        }

        $constraint->choices = $this->getChoices($value, $constraint);

        $allowedOptions = get_class_vars(Choice::class);
        $options = array_filter(
            get_object_vars($constraint),
            static function ($key) use ($allowedOptions) {
                return array_key_exists($key, $allowedOptions);
            },
            ARRAY_FILTER_USE_KEY
        );
        $choiceConstraint = new Choice($options);
        $choiceConstraint->groups = $constraint->groups;

        $this->context->getValidator()->validate(
            $this->getRealValue($value),
            $choiceConstraint
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

    abstract protected function getChoices($value, Choice $constraint): array;
}
