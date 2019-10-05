<?php
namespace App\Controller\Exercise\Record;

use App\Data\Pagination;
use App\Entity\Exercise;
use App\Handler\Record\ListRecordsHandler;
use App\Http\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/exercises/{exercise}/records", name="app_exercise_record_list", methods={"GET"})
 */
class ListRecordsController
{
    private ListRecordsHandler $handler;

    public function __construct(ListRecordsHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @IsGranted("EXERCISE_RECORDS_LIST", subject="exercise", statusCode=404)
     */
    public function __invoke(Request $request, Exercise $exercise)
    {
        $pagination = $this->handler->getPaginationHelper(
            $exercise,
            $request->query->getInt('page', 1)
        );

        $records = $this->handler->getRecords($exercise, $pagination);

        return new JsonResponse([
            'data' => $records,
            'pagination' => Pagination::fromPaginationHelper($pagination),
        ]);
    }
}
