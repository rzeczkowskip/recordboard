<?php
namespace App\Repository;

use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;

class ExerciseRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(string $id): ?Exercise
    {
        return $this->em->find(Exercise::class, $id);
    }

    /**
     * @return array|Exercise[]
     */
    public function getList(?string $userId = null): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('e');
        $qb->from(Exercise::class, 'e');

        if ($userId) {
            $qb->where('e.user = :user');
            $qb->setParameter('user', $userId);
        }

        $qb->orderBy('e.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
