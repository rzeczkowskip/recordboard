<?php
namespace App\Controller\User;

use App\Http\JsonResponse;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/user/me", name="app_user_me", defaults={"id": null})
 */
class MyProfileController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request, UserInterface $authUser): JsonResponse
    {
        $user = $this->userRepository->getProfileById(
            Uuid::fromString($authUser->getUsername())
        );

        return new JsonResponse([
            'data' => $user,
        ]);
    }
}
