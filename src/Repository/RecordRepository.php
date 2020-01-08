<?php
namespace App\Repository;

use App\DTO\Record\ListSearchCriteria;
use App\Entity\Record;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RecordRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRecordData(string $id): \App\Data\Record\Record
    {
        $query = sprintf(
            'SELECT NEW %s(e.id, r.earnedAt, r.values)
                FROM App:Record r
                JOIN r.exercise e
                WHERE r = :id',
            \App\Data\Record\Record::class,
        );

        $query = $this->em->createQuery($query);
        $query->setParameter('id', $id);
        return $query->getSingleResult();
    }

    /**
     * @return array|\App\Data\Record\Record[]
     */
    public function getRecords(ListSearchCriteria $searchCriteria): array
    {
        $select = sprintf(
            'NEW %s(e.id, r.earnedAt, r.values)',
            \App\Data\Record\Record::class
        );

        $qb = $this->getRecordsBuilder($searchCriteria->getExercise()->getId());

        $qb->select($select);

        $qb->setMaxResults($searchCriteria->getQueryLimit());
        $qb->setFirstResult($searchCriteria->getQueryOffset());

        return $qb->getQuery()->getResult();
    }

    public function getRecordsCount(string $exercise): int
    {
        $qb = $this->getRecordsBuilder($exercise);
        $qb->select('COUNT(r)');

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    private function getRecordsBuilder(string $exercise): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('r');
        $qb->from(Record::class, 'r');

        $qb->join('r.exercise', 'e');

        $qb->andWhere('r.exercise = :exercise');
        $qb->setParameter('exercise', $exercise);

        return $qb;
    }
}
