<?php
namespace App\Controller\User;

use App\Handler\User\GenerateAuthUserApiTokenHandler;
use App\Http\JsonResponse;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/user/auth", name="app_user_auth", methods={"POST"})
 */
class AuthController
{
    private UserRepository $userRepository;
    private GenerateAuthUserApiTokenHandler $apiTokenHandler;

    public function __construct(UserRepository $userRepository, GenerateAuthUserApiTokenHandler $apiTokenHandler)
    {
        $this->userRepository = $userRepository;
        $this->apiTokenHandler = $apiTokenHandler;
    }

    public function __invoke(Request $request, UserInterface $authUser)
    {
        $userId = Uuid::fromString($authUser->getUsername());
        $user = $this->userRepository->getProfileById($userId);
        $apiToken = $this->apiTokenHandler->generateToken($userId);

        return new JsonResponse([
            'data' => [
                'user' => $user,
                'token' => $apiToken,
            ]
        ]);
    }
}
