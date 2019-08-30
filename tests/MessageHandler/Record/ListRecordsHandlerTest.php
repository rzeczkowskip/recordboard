<?php
namespace App\Tests\MessageHandler\Record;

use App\DTO\Record\ListSearchCriteria;
use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Message\PaginationHelper;
use App\Message\Record\CreateRecord;
use App\Message\Record\ListRecords;
use App\MessageHandler\Record\CreateRecordHandler;
use App\MessageHandler\Record\ListRecordsHandler;
use App\Repository\RecordRepository;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ListRecordsHandlerTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private EntityManagerInterface $em;

    /**
     * @var RecordRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private RecordRepository $recordRepository;

    /**
     * @var PaginationHelper|\PHPUnit\Framework\MockObject\MockObject
     */
    private PaginationHelper $pagination;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->recordRepository = $this->createMock(RecordRepository::class);
        $this->pagination = $this->createMock(PaginationHelper::class);
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->recordRepository, $this->pagination);
    }

    public function testListRecordsWithoutExercise(): void
    {
        $userId = Uuid::uuid4();
        $authUser = new AuthUser($userId, '', '');

        $user = $this->createMock(User::class);
        $recordsCount = 2;
        $records = [
            ['record1'],
            ['record2']
        ];

        $this->em
            ->expects(static::once())
            ->method('getReference')
            ->with(User::class, $userId)
            ->willReturn($user);

        $this->recordRepository
            ->expects(static::once())
            ->method('getRecordsCount')
            ->with(self::isInstanceOf(ListSearchCriteria::class))
            ->willReturn($recordsCount);

        $this->pagination
            ->expects(static::once())
            ->method('setTotalItems')
            ->with($recordsCount);

        $this->recordRepository
            ->expects(static::once())
            ->method('getRecords')
            ->with(static::isInstanceOf(ListSearchCriteria::class))
            ->willReturn($records);

        $message = new ListRecords($authUser, $this->pagination);
        $handler = new ListRecordsHandler($this->em, $this->recordRepository);
        $result = $handler($message);

        static::assertEquals($records, $result);
    }

    public function testListRecordsWithExerciseFilter(): void
    {
        $userId = Uuid::uuid4();
        $authUser = new AuthUser($userId, '', '');
        $exerciseId = Uuid::uuid4();

        $user = $this->createMock(User::class);
        $exercise = $this->createMock(Exercise::class);

        $recordsCount = 2;
        $records = [
            ['record1'],
            ['record2']
        ];

        $this->em
            ->expects(static::exactly(2))
            ->method('getReference')
            ->withConsecutive(
                [User::class, $userId],
                [Exercise::class, $exerciseId]
            )
            ->willReturnOnConsecutiveCalls($user, $exercise);

        $this->recordRepository
            ->expects(static::once())
            ->method('getRecordsCount')
            ->with(self::isInstanceOf(ListSearchCriteria::class))
            ->willReturn($recordsCount);

        $this->pagination
            ->expects(static::once())
            ->method('setTotalItems')
            ->with($recordsCount);

        $this->recordRepository
            ->expects(static::once())
            ->method('getRecords')
            ->with(static::isInstanceOf(ListSearchCriteria::class))
            ->willReturn($records);

        $message = new ListRecords($authUser, $this->pagination);
        $message->exercise = $exerciseId;

        $handler = new ListRecordsHandler($this->em, $this->recordRepository);
        $result = $handler($message);

        static::assertEquals($records, $result);
    }

}
