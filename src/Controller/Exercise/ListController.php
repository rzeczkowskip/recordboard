<?php
namespace App\Controller\Exercise;

use App\Repository\ExerciseRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => $this->exerciseRepository->getExercisesList(),
        ]);
    }
}
