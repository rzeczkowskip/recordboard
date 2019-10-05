<?php
namespace App\Controller\User;

use App\Handler\User\RegisterUserHandler;
use App\Http\RequestMapper;
use App\DTO\User\RegisterUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/register", name="app_user_register", methods={"POST"})
 */
class RegisterController
{
    private RequestMapper $requestMapper;
    private RegisterUserHandler $registerUserHandler;

    public function __construct(RequestMapper $requestMapper, RegisterUserHandler $registerUserHandler)
    {
        $this->requestMapper = $requestMapper;
        $this->registerUserHandler = $registerUserHandler;
    }

    public function __invoke(Request $request)
    {
        $data = new RegisterUser();
        $this->requestMapper->mapToObject($request, $data);

        $this->registerUserHandler->register($data);

        return new Response('', Response::HTTP_CREATED);
    }
}
