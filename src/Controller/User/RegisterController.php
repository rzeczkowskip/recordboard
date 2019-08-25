<?php
namespace App\Controller\User;

use App\Message\User\RegisterUser;
use App\Messenger\HandleTrait;
use App\Messenger\Middleware\Configuration\SerializedRequestStamp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ValidationStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/register", name="app_user_register", methods={"POST"})
 */
class RegisterController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request)
    {
        $this->handle(
            new Envelope(
                new RegisterUser(),
                [
                    new ValidationStamp([]),
                    new SerializedRequestStamp($request->getContent()),
                ]
            ),
            $this->messageBus
        );

        return new Response('', Response::HTTP_CREATED);
    }
}
