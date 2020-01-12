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

    /**
     * @return array|Record[]
     */
    public function searchRecords(ListSearchCriteria $searchCriteria): array
    {
        $qb = $this->getRecordsBuilder($searchCriteria->getExercise()->getId());

        $qb->setMaxResults($searchCriteria->getQueryLimit());
        $qb->setFirstResult($searchCriteria->getQueryOffset());

        $qb->orderBy('r.earnedAt', 'DESC');

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
