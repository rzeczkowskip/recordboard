<?php
namespace App\Controller\Api\V1\Exercise;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/exercises/{exercise}", name="app_api_v1_exercise_delete", methods={"DELETE"})
 */
class DeleteController
{
    private ExerciseRepository $exerciseRepository;

    public function __construct(ExerciseRepository $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    public function __invoke(Request $request, Exercise $exercise): Response
    {
        $this->exerciseRepository->deleteExercise($exercise);

        return new Response('', Response::HTTP_OK);
    }
}
