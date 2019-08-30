<?php

namespace App\Validator\Constraints;

use App\Repository\ExerciseRepository;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

class ExerciseChoiceValidator extends AbstractDynamicChoiceValidator
{
    private ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository, ?ChoiceValidator $choiceValidator = null)
    {
        parent::__construct($choiceValidator);
        $this->exerciseRepository = $exerciseRepository;
    }

    protected function getChoices($value, Choice $constraint): array
    {
        $exercises = $this->exerciseRepository->getExercisesList();
        $choices = [];
        foreach ($exercises as $exercise) {
            $choices[] = $exercise->id->toString();
        }

        return $choices;
    }
}
