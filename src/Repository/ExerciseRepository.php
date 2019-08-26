<?php
namespace App\Repository;

use App\Entity\Exercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    /**
     * @return array|\App\DTO\Exercise\Exercise[]
     */
    public function getExercisesList(): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select(sprintf(
            'NEW %s(e.id, e.name, e.attributes)',
            \App\DTO\Exercise\Exercise::class
        ));

        $qb->orderBy('e.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
