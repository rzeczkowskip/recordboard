<?php
namespace App\Controller\Api\V1\Exercise;

use App\DTO\Exercise\CreateExercise;
use App\Handler\Exercise\CreateExerciseHandler;
use App\Http\JsonResponse;
use App\Http\RequestMapper;
use App\Model\Exercise\Exercise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/exercises", name="app_api_v1_exercise_create", methods={"POST"})
 */
class CreateController
{
    private RequestMapper $requestMapper;
    private CreateExerciseHandler $createExerciseHandler;

    public function __construct(RequestMapper $requestMapper, CreateExerciseHandler $createExerciseHandler) {
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

        $exercise = $this->createExerciseHandler->createExercise($data);

        return new JsonResponse(
            [
                'data' => Exercise::fromExercise($exercise),
            ],
            Response::HTTP_CREATED,
        );
    }
}
