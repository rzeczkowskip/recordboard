<?php
namespace App\Controller\Api\V1\Exercise;

use App\DTO\Exercise\CreateExercise;
use App\Handler\Exercise\CreateExerciseHandler;
use App\Http\JsonResponse;
use App\Http\RequestMapper;
use App\Repository\ExerciseRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/exercises", name="app_api_v1_exercise_create", methods={"POST"})
 */
class CreateController
{
    private ExerciseRepository $exerciseRepository;
    private RequestMapper $requestMapper;
    private CreateExerciseHandler $createExerciseHandler;

    public function __construct(
        RequestMapper $requestMapper,
        CreateExerciseHandler $createExerciseHandler,
        ExerciseRepository $exerciseRepository
    ) {
        $this->exerciseRepository = $exerciseRepository;
        $this->requestMapper = $requestMapper;
        $this->createExerciseHandler = $createExerciseHandler;
    }

    public function __invoke(Request $request, UserInterface $authUser)
    {
        $data = new CreateExercise();
        $data->user = $authUser->getUsername();

        $this->requestMapper->mapToObject($request, $data, [
            'attributes' => ['name', 'attributes']
        ]);

        $id = $this->createExerciseHandler->createExercise($data);

        return new JsonResponse(
            [
                'data' => $this->exerciseRepository->getExerciseData($id),
            ],
            Response::HTTP_CREATED,
        );
    }
}
