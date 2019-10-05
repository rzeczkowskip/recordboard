<?php
namespace App\Controller\Exercise;

use App\Http\JsonResponse;
use App\Repository\ExerciseRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/exercises", name="app_exercise_list")
 */
class ListController
{
    private ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    public function __invoke(UserInterface $user): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->exerciseRepository->getExercisesList(
                Uuid::fromString($user->getUsername())
            ),
        ]);
    }
}
