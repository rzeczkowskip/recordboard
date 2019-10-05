<?php
namespace App\Tests\MessageHandler\Record;

use App\DTO\Record\CreateRecord;
use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Handler\Record\CreateRecordHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateRecordHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->validator, $this->em);
    }

    public function testCreateRecordFailValidation(): void
    {
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations
            ->method('count')
            ->willReturn(1);

        $data = new CreateRecord();

        $this->validator
            ->method('validate')
            ->with($data)
            ->willReturn($violations);

        $this->em
            ->expects(static::never())
            ->method('persist');

        $this->expectException(ValidationException::class);

        $handler = new CreateRecordHandler($this->validator, $this->em);
        $handler->createRecord($data);
    }

    public function testCreateRecord(): void
    {
        $user = new User('', '', '');
        $exercise = new Exercise($user, '', []);
        $exerciseId = $exercise->getId();

        $data = new CreateRecord();
        $data->exercise = $exerciseId;
        $data->earnedAt = new \DateTime();
        $data->values = [];

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations
            ->method('count')
            ->willReturn(0);

        $this->validator
            ->method('validate')
            ->with($data)
            ->willReturn($violations);

        $this->em
            ->method('getReference')
            ->with(Exercise::class, $exerciseId)
            ->willReturn($exercise);

        $this->em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(Record::class));

        $this->em
            ->expects(static::once())
            ->method('flush');

        $handler = new CreateRecordHandler($this->validator, $this->em);
        $handler->createRecord($data);
    }
}
