<?php

namespace App\Validator\Constraints;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;
use Ramsey\Uuid\UuidInterface;
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

    public function __construct(ExerciseRepository $exerciseRepository, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->exerciseRepository = $exerciseRepository;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RecordValues) {
            throw new UnexpectedTypeException($constraint, RecordValues::class);
        }

        /** @var Exercise $exercise */
        $exercise = $this->getPropertyAccessor()->getValue($this->context->getObject(), $constraint->exercise);
        if ($exercise instanceof UuidInterface) {
            $exercise = $this->exerciseRepository->findById($exercise);
        }

        if (!$exercise instanceof Exercise) {
            throw new UnexpectedTypeException(
                is_object($exercise) ? get_class($exercise) : gettype($exercise), Exercise::class
            );
        }

        $collectionFields = [];
        foreach ($exercise->getAttributes() as $attribute) {
            $collectionFields[$attribute] = [
                new Assert\NotBlank(),
                new Assert\Type('integer'),
                new Assert\Positive()
            ];
        }

        $options = $constraint->getOptions();
        $options['fields'] = $collectionFields;
        $collectionConstraint = new Assert\Collection($options);

        $this->context->getValidator()->validate(
            $value,
            $collectionConstraint
        );
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (!isset($this->propertyAccessor)) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
