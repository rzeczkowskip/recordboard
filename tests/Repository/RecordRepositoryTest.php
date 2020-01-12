<?php
namespace App\Tests\Repository;

use App\DTO\Record\ListSearchCriteria;
use App\Entity\Exercise;
use App\Entity\Record;
use App\Handler\PaginationHelper;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecordRepositoryTest extends KernelTestCase
{
    private RecordRepository $repository;
    private Exercise $exercise;
    private array $existingRecord;

    protected function setUp(): void
    {
        static::bootKernel();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $this->repository = self::$container->get(RecordRepository::class);
        $this->exercise = $em->createQuery('SELECT e FROM App:Exercise e')->getSingleResult();
        $this->existingRecord = $em
            ->createQuery('SELECT r FROM App:Record r WHERE r.exercise = :exercise')
            ->setParameter('exercise', $this->exercise)
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getSingleResult(Query::HYDRATE_ARRAY);
    }

    protected function tearDown(): void
    {
        unset($this->repository, $this->exercise, $this->existingRecord);
        parent::tearDown();
    }

    public function testSearchRecords(): void
    {
        $pagination = new PaginationHelper(1, 1, 1);
        $criteria = new ListSearchCriteria($this->exercise, $pagination);

        $records = $this->repository->searchRecords($criteria);

        static::assertCount(1, $records);

        $record = reset($records);
        static::assertEquals($this->existingRecord['values'], $record->getValues());
        static::assertEquals($this->existingRecord['exercise_id'], $record->getExercise()->getId());
    }

    public function testSearchRecordsCount(): void
    {
        $result = $this->repository->getRecordsCount($this->exercise->getId());

        static::assertEquals(1, $result);
    }
}
