<?php

namespace App\Validator\Constraints;

use App\Repository\ExerciseRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;

class ExerciseChoiceValidator extends AbstractDynamicChoiceValidator
{
    private ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    /**
     * @param object $value
     * @param Choice|ExerciseChoice $constraint
     *
     * @return array
     */
    protected function getChoices($value, Choice $constraint): array
    {
        $user = null;
        if ($constraint->user) {
            $user = $this->getPropertyAccessor()->getValue($value, $constraint->user);
        }

        $exercises = $this->exerciseRepository->getExercisesList($user);
        $choices = [];
        foreach ($exercises as $exercise) {
            $choices[] = $exercise->getId()->toString();
        }

        return $choices;
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (!isset($this->propertyAccessor)) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
