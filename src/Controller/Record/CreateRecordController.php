<?php
namespace App\Controller\Record;

use App\Message\Record\CreateRecord;
use App\Messenger\HandleTrait;
use App\Messenger\Middleware\Configuration\SerializedRequestStamp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ValidationStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/records", name="app_record_create", methods={"POST"})
 */
class CreateRecordController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request, UserInterface $authUser)
    {
        $this->handle(
            new Envelope(
                new CreateRecord($authUser),
                [
                    new SerializedRequestStamp($request->getContent()),
                    new ValidationStamp([]),
                ]
            ),
            $this->messageBus
        );

        return new Response('', Response::HTTP_CREATED);
    }
}
