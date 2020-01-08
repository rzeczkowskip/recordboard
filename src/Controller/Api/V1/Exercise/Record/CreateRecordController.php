<?php
namespace App\Controller\Api\V1\Exercise\Record;

use App\DTO\Record\CreateRecord;
use App\Entity\Exercise;
use App\Handler\Record\CreateRecordHandler;
use App\Http\JsonResponse;
use App\Http\RequestMapper;
use App\Repository\RecordRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/exercises/{exercise}/records", name="app_api_v1_exercise_record_create", methods={"POST"})
 */
class CreateRecordController
{
    private RequestMapper $requestMapper;
    private CreateRecordHandler $createRecordHandler;
    private RecordRepository $recordRepository;

    public function __construct(
        RequestMapper $requestMapper,
        CreateRecordHandler $createRecordHandler,
        RecordRepository $recordRepository
    ) {
        $this->requestMapper = $requestMapper;
        $this->createRecordHandler = $createRecordHandler;
        $this->recordRepository = $recordRepository;
    }

    /**
     * @IsGranted("EXERCISE_RECORDS_CREATE", subject="exercise", statusCode=404)
     */
    public function __invoke(Request $request, Exercise $exercise, UserInterface $authUser)
    {
        $data = new CreateRecord();
        $data->exercise = $exercise->getId();
        $data->user = $authUser->getUsername();

        $this->requestMapper->mapToObject($request, $data, [
            'attributes' => ['earnedAt', 'values']
        ]);

        $recordId = $this->createRecordHandler->createRecord($data);

        return new JsonResponse(
            [
                'data' => $this->recordRepository->getRecordData($recordId),
            ],
            Response::HTTP_CREATED
        );
    }
}
