<?php
namespace App\Controller\Record;

use App\DTO\Pagination;
use App\DTO\Record\Record;
use App\Exception\PageOverLimitPaginationException;
use App\Message\PaginationHelper;
use App\Message\Record\ListRecords;
use App\Messenger\HandleTrait;
use App\Messenger\Middleware\Configuration\ArrayRequestStamp;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ValidationStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/records", name="app_record_list", methods={"GET"})
 */
class ListRecordsController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request, UserInterface $authUser)
    {
        $query = [
            'exercise' => $request->query->get('exercise', null),
        ];

        $pagination = new PaginationHelper(
            ListRecords::ITEMS_PER_PAGE,
            $request->query->getInt('page', 1),
        );

        /** @var array|Record[] $result */
        $result = $this->handle(
            new Envelope(
                new ListRecords($authUser, $pagination),
                [
                    new ArrayRequestStamp($query),
                    new ValidationStamp([]),
                ]
            ),
            $this->messageBus
        );

        return new JsonResponse([
            'data' => $result,
            'pagination' => Pagination::fromPaginationHelper($pagination),
        ]);
    }
}
