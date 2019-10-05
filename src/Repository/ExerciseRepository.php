<?php
namespace App\Repository;

use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

class ExerciseRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(UuidInterface $id): ?Exercise
    {
        return $this->em->find(Exercise::class, $id);
    }

    public function getExerciseData(UuidInterface $exercise): \App\Data\Exercise\Exercise
    {
        $query = sprintf(
            'SELECT NEW %s(e.id, e.name, e.attributes) 
                FROM App:Exercise e 
                WHERE e.id = :exercise',
            \App\Data\Exercise\Exercise::class,
        );

        return $this->em
            ->createQuery($query)
            ->setParameter('exercise', $exercise)
            ->getSingleResult();
    }
    /**
     * @return array|\App\Data\Exercise\Exercise[]
     */
    public function getExercisesList(?UuidInterface $user = null): array
    {
        $select = sprintf(
            'NEW %s(e.id, e.name, e.attributes)',
            \App\Data\Exercise\Exercise::class
        );

        $qb = $this->em->createQueryBuilder();

        $qb->select($select);
        $qb->from(Exercise::class, 'e');

        if ($user) {
            $qb->where('e.user = :user');
            $qb->setParameter('user', $user);
        }

        $qb->orderBy('e.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
