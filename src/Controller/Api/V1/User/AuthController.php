<?php
namespace App\Controller\Api\V1\User;

use App\Handler\User\GenerateAuthUserApiTokenHandler;
use App\Http\JsonResponse;
use App\Model\User\Profile;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/user/auth", name="app_api_v1_user_auth", methods={"POST"})
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
        $userId = $authUser->getUsername();
        $user = $this->userRepository->getUserById($userId);
        $apiToken = $this->apiTokenHandler->generateToken($userId);

        return new JsonResponse([
            'data' => [
                'user' => Profile::fromUser($user),
                'token' => $apiToken->getToken(),
            ]
        ]);
    }
}
