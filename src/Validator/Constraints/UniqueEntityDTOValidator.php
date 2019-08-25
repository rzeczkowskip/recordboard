<?php

namespace App\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityDTOValidator extends ConstraintValidator
{
    private ManagerRegistry $registry;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(ManagerRegistry $registry, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->registry = $registry;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntityDTO) {
            throw new UnexpectedTypeException($constraint, UniqueEntityDTO::class);
        }

        $propertyAccessor = $this->getPropertyAccessor();

        $fields = $propertyAccessor->getValue($object, $constraint->fields);

        $entityClass = $constraint->entityClass;
        $entity = $constraint->entity;

        if ($entity !== null) {
            $entity = $propertyAccessor->getValue($object, $entity);
            $entityClass = get_class($entity);
        }

        $em = $this->registry->getManagerForClass($entityClass);
        if (!$em) {
            throw new ConstraintDefinitionException(sprintf(
                'Unable to find the object manager associated with an entity of class "%s".',
                $entityClass
            ));
        }

        $repository = $em->getRepository($entityClass);

        $result = $repository->findBy($fields);
        if (count($result) === 0 || (count($result) === 1 && $entity && reset($result) === $entity)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->setCode($constraint::NOT_UNIQUE_ERROR)
            ->addViolation();
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
