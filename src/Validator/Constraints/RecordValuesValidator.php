<?php

namespace App\Validator\Constraints;

use App\Repository\ExerciseRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RecordValuesValidator extends ConstraintValidator
{
    private ExerciseRepository $exerciseRepository;
    private ?PropertyAccessorInterface $propertyAccessor;
    private ?Assert\CollectionValidator $collectionValidator;

    public function __construct(ExerciseRepository $exerciseRepository, ?Assert\CollectionValidator $collectionValidator = null, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->exerciseRepository = $exerciseRepository;
        $this->collectionValidator = $collectionValidator;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RecordValues) {
            throw new UnexpectedTypeException($constraint, RecordValues::class);
        }

        $exerciseId = $this->getPropertyAccessor()->getValue($this->context->getObject(), $constraint->exercise);

        try {
            $exercise = $this->exerciseRepository->getExerciseById((string) $exerciseId);
        } catch (NoResultException $e) {
            return;
        }

        $collectionFields = [];
        foreach ($exercise->attributes as $attribute) {
            $collectionFields[$attribute] = [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive()
            ];
        }

        $options = $constraint->getOptions();
        $options['fields'] = $collectionFields;
        $collectionConstraint = new Assert\Collection($options);

        $validator = $this->getValidator();

        $validator->initialize($this->context);
        $validator->validate($value, $collectionConstraint);
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (!isset($this->propertyAccessor)) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }

    private function getValidator(): Assert\CollectionValidator
    {
        if (!isset($this->collectionValidator)) {
            $this->collectionValidator = new Assert\CollectionValidator();
        }

        return $this->collectionValidator;
    }
}
