<?php
namespace App\Tests\Handler\Record;

use App\DTO\Record\ListSearchCriteria;
use App\Entity\Exercise;
use App\Entity\User;
use App\Handler\PaginationHelper;
use App\Handler\Record\ListRecordsHandler;
use App\Model\Record\Record;
use App\Repository\RecordRepository;
use PHPUnit\Framework\TestCase;

class ListRecordsHandlerTest extends TestCase
{
    /**
     * @var RecordRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private RecordRepository $recordRepository;

    protected function setUp(): void
    {
        $this->recordRepository = $this->createMock(RecordRepository::class);
    }

    protected function tearDown(): void
    {
        unset($this->recordRepository);
    }

    public function testGetPaginationHelper(): void
    {
        $totalItems = 10;
        $page = 1;
        $itemsPerPage = 2;
        $expectedPageCount = 5;

        $exercise = new Exercise(
            new User('', '', ''),
            '',
            []
        );

        $this->recordRepository
            ->method('getRecordsCount')
            ->willReturn($totalItems);

        $handler = new ListRecordsHandler($this->recordRepository);
        $pagination = $handler->getPaginationHelper($exercise, $page, $itemsPerPage);

        static::assertEquals($totalItems, $pagination->getTotalItems());
        static::assertEquals($page, $pagination->getPage());
        static::assertEquals($expectedPageCount, $pagination->getPages());
        static::assertEquals($itemsPerPage, $pagination->getItemsPerPage());
    }

    public function testGetRecords(): void
    {
        $exercise = new Exercise(
            new User('', '', ''),
            '',
            [],
        );

        $itemsPerPage = 20;
        $queryOffset = 20;
        $pagination = new PaginationHelper(100, 2, $itemsPerPage);

        $records = [
            new Record(uuid_v4(), new \DateTime(), []),
        ];

        $this->recordRepository
            ->expects(static::once())
            ->method('getRecords')
            ->with(static::callback(function (ListSearchCriteria $searchCriteria) use ($exercise, $itemsPerPage, $queryOffset) {
                return $searchCriteria->getExercise() === $exercise &&
                    $searchCriteria->getQueryLimit() === $itemsPerPage &&
                    $searchCriteria->getQueryOffset() === $queryOffset;
            }))
            ->willReturn($records);

        $handler = new ListRecordsHandler($this->recordRepository);
        $result = $handler->getRecords($exercise, $pagination);

        static::assertEquals($records, $result);
    }
}
