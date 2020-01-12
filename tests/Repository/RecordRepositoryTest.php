<?php
namespace App\Tests\Repository;

use App\DTO\Record\ListSearchCriteria;
use App\Entity\Exercise;
use App\Entity\Record;
use App\Handler\PaginationHelper;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecordRepositoryTest extends KernelTestCase
{
    private RecordRepository $repository;
    private Exercise $exercise;
    private array $values;
    private \DateTimeInterface $earnedAt;

    private Record $record;

    protected function setUp(): void
    {
        static::bootKernel();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get('doctrine')->getManager();
        $this->repository = self::$container->get(RecordRepository::class);

        /** @var Exercise $exercise */
        $exercise = $em->createQuery('SELECT e FROM App:Exercise e')->getSingleResult();
        $recordValues = array_combine($exercise->getAttributes(), array_fill(0, count($exercise->getAttributes()), 1));
        $earnedAt = new \DateTime('now');

        $record = new Record($exercise, $earnedAt, $recordValues);
        $em->persist($record);
        $em->flush();

        $this->record = $record;
        $this->exercise = $exercise;
        $this->values = $recordValues;
        $this->earnedAt = $earnedAt;
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->existingRecord);
        self::ensureKernelShutdown();
    }

    public function testGetRecordData(): void
    {
        $result = $this->repository->getRecordData($this->record->getId());

        static::assertEquals($this->values, $result->getValues());
        static::assertEquals($this->exercise->getId(), $result->getExercise());
        static::assertEqualsWithDelta($this->earnedAt, $result->getEarnedAt(), 1);
    }

    public function testGetRecords(): void
    {
        $pagination = new PaginationHelper(1, 1, 1);
        $criteria = new ListSearchCriteria($this->exercise, $pagination);

        $records = $this->repository->getRecords($criteria);

        static::assertCount(1, $records);

        /** @var \App\Model\Record\Record $record */
        $record = reset($records);
        static::assertEquals($this->values, $record->getValues());
        static::assertEquals($this->exercise->getId(), $record->getExercise());
        static::assertEqualsWithDelta($this->earnedAt, $record->getEarnedAt(), 1);
    }

    public function testGetRecordsCount(): void
    {
        $result = $this->repository->getRecordsCount($this->exercise->getId());

        static::assertEquals(1, $result);
    }
}
