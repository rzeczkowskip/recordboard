<?php

namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\UniqueEntityDTO;
use App\Validator\Constraints\UniqueEntityDTOValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueEntityDTOValidatorTest extends TestCase
{
    /**
     * @var ManagerRegistry|\PHPUnit\Framework\MockObject\MockObject
     */
    private ManagerRegistry $registry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PropertyAccessorInterface
     */
    private PropertyAccessorInterface $propertyAccessor;

    /**
     * @var UniqueEntityDTO
     */
    private UniqueEntityDTO $constraint;

    /**
     * @var QueryBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private QueryBuilder $qb;

    public function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $this->constraint = new UniqueEntityDTO(['entityClass' => '']);
        $this->qb = $this->createMock(QueryBuilder::class);
    }

    public function tearDown(): void
    {
        unset($this->registry, $this->propertyAccessor);
    }

    public function testValidateInvalidConstraintThorwsException(): void
    {
        $constraint = new class extends Constraint {};

        $this->expectException(UnexpectedTypeException::class);

        $validator = new UniqueEntityDTOValidator($this->registry, $this->propertyAccessor);
        $validator->validate(null, $constraint);
    }

    public function testValidateFieldsMethodNotCallableThrowsException(): void
    {
        $this->expectException(ConstraintDefinitionException::class);

        $this->constraint->fields = 'test';

        $validator = new UniqueEntityDTOValidator($this->registry, $this->propertyAccessor);
        $validator->validate(new class {}, $this->constraint);
    }

    /**
     * @dataProvider validateNoManagerForEntityProvider
     */
    public function testValidateNoManagerForEntity(object $object, ?UniqueEntityDTO $constraint, ?object $entity, string $entityClass): void
    {
        if (!$constraint) {
            $constraint = $this->constraint;
            $constraint->entityClass = $entityClass;
        }

        $propertyAccessorArguments = [
            [$object, 'uniqueFields']
        ];

        $propertyAccessorReturnValues = [[]];

        if ($entity !== null) {
            $propertyAccessorArguments[] = [$object, $constraint->entity];
            $propertyAccessorReturnValues[] = $entity;
        }

        $this->propertyAccessor
            ->expects(static::exactly(count($propertyAccessorReturnValues)))
            ->method('getValue')
            ->withConsecutive(...$propertyAccessorArguments)
            ->willReturnOnConsecutiveCalls(...$propertyAccessorReturnValues);

        $this->registry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->with($entityClass)
            ->willReturn(null);

        $this->expectException(ConstraintDefinitionException::class);

        $validator = new UniqueEntityDTOValidator($this->registry, $this->propertyAccessor);
        $validator->validate($object, $constraint);
    }

    public function validateNoManagerForEntityProvider(): \Generator
    {
        yield [
            new class {},
            null,
            null,
            'invalid',
        ];

        $entityClass = new class {};
        yield [
            new class ($entityClass) {
                private object $entity;

                public function __construct(object $entity)
                {
                    $this->entity = $entity;
                }

                public function getEntity(): object
                {
                    return $this->entity;
                }
            },
            new UniqueEntityDTO(['entity' => 'getEntity']),
            $entityClass,
            get_class($entityClass),
        ];
    }

    public function testValidateNoViolations(): void
    {
        $entityClass = '\Entity';
        $uniqueFields = ['email' => 'test@example.com'];

        $testObject = new class ($uniqueFields) {
            private array $uniqueFields;

            public function __construct(array $uniqueFields)
            {
                $this->uniqueFields = $uniqueFields;
            }

            public function uniqueFields(): array
            {
                return $this->uniqueFields;
            }
        };

        $this->constraint->entityClass = $entityClass;

        $em = $this->createMock(EntityManagerInterface::class);
        $query = $this->createMock(AbstractQuery::class);

        $this->registry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->with($entityClass)
            ->willReturn($em);

        $this->propertyAccessor
            ->expects(static::once())
            ->method('getValue')
            ->with($testObject, 'uniqueFields')
            ->willReturn($uniqueFields);

        $em
            ->method('createQueryBuilder')
            ->willReturn($this->qb);

        $this->qb
            ->expects(static::once())
            ->method('select')
            ->willReturnSelf();

        $this->qb
            ->expects(static::once())
            ->method('from')
            ->with($entityClass)
            ->willReturnSelf();

        $this->qb
            ->method('andWhere')
            ->willReturnSelf();

        $this->qb
            ->expects(static::once())
            ->method('setParameters')
            ->with($uniqueFields);

        $this->qb
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->method('getResult')
            ->willReturn([]);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects(static::never())
            ->method('buildViolation');

        $validator = new UniqueEntityDTOValidator($this->registry, $this->propertyAccessor);
        $validator->initialize($context);
        $validator->validate($testObject, $this->constraint);
    }

    public function testValidateHasError(): void
    {
        $entityClass = '\Entity';
        $uniqueFields = ['email' => 'test@example.com'];

        $testObject = new class ($uniqueFields) {
            private array $uniqueFields;

            public function __construct(array $uniqueFields)
            {
                $this->uniqueFields = $uniqueFields;
            }

            public function uniqueFields(): array
            {
                return $this->uniqueFields;
            }
        };

        $message = 'error';
        $errorPath = 'test';

        $this->constraint->entityClass = $entityClass;
        $this->constraint->message = $message;
        $this->constraint->errorPath = $errorPath;

        $em = $this->createMock(EntityManagerInterface::class);
        $query = $this->createMock(AbstractQuery::class);

        $this->propertyAccessor
            ->expects(static::once())
            ->method('getValue')
            ->with($testObject, 'uniqueFields')
            ->willReturn($uniqueFields);

        $this->registry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->with($entityClass)
            ->willReturn($em);

        $em
            ->method('createQueryBuilder')
            ->willReturn($this->qb);

        $this->qb
            ->expects(static::once())
            ->method('select')
            ->willReturnSelf();

        $this->qb
            ->expects(static::once())
            ->method('from')
            ->with($entityClass)
            ->willReturnSelf();

        $this->qb
            ->method('andWhere')
            ->willReturnSelf();

        $this->qb
            ->expects(static::once())
            ->method('setParameters')
            ->with($uniqueFields);

        $this->qb
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->method('getResult')
            ->willReturn([new class {}]);

        $context = $this->createMock(ExecutionContextInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $context
            ->expects(static::once())
            ->method('buildViolation')
            ->with($message)
            ->willReturn($violationBuilder);

        $violationBuilder
            ->expects(static::once())
            ->method('atPath')
            ->with($errorPath)
            ->willReturnSelf();

        $violationBuilder
            ->expects(static::once())
            ->method('setCode')
            ->with($this->constraint::NOT_UNIQUE_ERROR)
            ->willReturnSelf();

        $violationBuilder
            ->expects(static::once())
            ->method('addViolation');

        $validator = new UniqueEntityDTOValidator($this->registry, $this->propertyAccessor);
        $validator->initialize($context);
        $validator->validate($testObject, $this->constraint);
    }

    public function testValidateEntitySameAsResult(): void
    {
        $entity = new class {};
        $uniqueFields = ['email' => 'test'];
        $testObject = new class ($entity, $uniqueFields) {
            public object $entity;
            public array $uniqueFields;

            public function __construct(object $entity, array $uniqueFields)
            {
                $this->entity = $entity;
                $this->uniqueFields = $uniqueFields;
            }
        };

        $this->constraint->entity = 'entity';

        $em = $this->createMock(EntityManagerInterface::class);
        $query = $this->createMock(AbstractQuery::class);

        $this->propertyAccessor
            ->expects(static::exactly(2))
            ->method('getValue')
            ->withConsecutive(
                [$testObject, 'uniqueFields'],
                [$testObject, 'entity']
            )
            ->willReturnOnConsecutiveCalls(
                $uniqueFields,
                $entity
            );

        $this->registry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->willReturn($em);

        $em
            ->method('createQueryBuilder')
            ->willReturn($this->qb);

        $this->qb
            ->expects(static::once())
            ->method('select')
            ->willReturnSelf();

        $this->qb
            ->method('from')
            ->willReturnSelf();

        $this->qb
            ->method('andWhere')
            ->willReturnSelf();

        $this->qb
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->method('getResult')
            ->willReturn([$entity]);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects(static::never())
            ->method('buildViolation');

        $validator = new UniqueEntityDTOValidator($this->registry, $this->propertyAccessor);
        $validator->initialize($context);
        $validator->validate($testObject, $this->constraint);
    }
}
