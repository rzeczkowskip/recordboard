<?php

namespace App\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
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

        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass($entityClass);
        if (!$em) {
            throw new ConstraintDefinitionException(sprintf(
                'Unable to find the object manager associated with an entity of class "%s".',
                $entityClass
            ));
        }

        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from($entityClass, 'e');

        foreach ($fields as $field => $value) {
            $qb->andWhere(sprintf(
                'e.%1$s = :%1$s',
                $field
            ));
        }

        $qb->setParameters($fields);
        $result = $qb->getQuery()->getResult();

        if (count($result) === 0 || (count($result) === 1 && $entity && reset($result) === $entity)) {
            return;
        }

        $violationBuilder = $this->context->buildViolation($constraint->message);
        $violationBuilder->atPath($constraint->errorPath);
        $violationBuilder->setCode($constraint::NOT_UNIQUE_ERROR);
        $violationBuilder->addViolation();
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
