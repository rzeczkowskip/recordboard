<?php
namespace App\MessageHandler\Exercise;

use App\Entity\Exercise;
use App\Message\Exercise\CreateExercise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateExerciseHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __invoke(CreateExercise $createExercise): void
    {
        $exercise = new Exercise($createExercise->name, $createExercise->attributes);

        $this->em->persist($exercise);
        $this->em->flush();
    }
}
