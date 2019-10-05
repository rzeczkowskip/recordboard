<?php
namespace App\Handler\Exercise;

use App\DTO\Exercise\CreateExercise;
use App\Entity\Exercise;
use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateExerciseHandler
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->em = $entityManager;
    }

    public function createExercise(CreateExercise $createExercise): UuidInterface

    {
        if (($violations = $this->validator->validate($createExercise)) && $violations->count()) {
            throw new ValidationException($violations);
        }

        $user = $this->em->getReference(
            User::class,
            $createExercise->user
        );

        $exercise = new Exercise($user, $createExercise->name, $createExercise->attributes);

        $this->em->persist($exercise);
        $this->em->flush();

        return $exercise->getId();
    }
}
