<?php
namespace App\Controller\User;

use App\Entity\UserApiToken;
use App\Message\User\GenerateAuthUserApiToken;
use App\Messenger\HandleTrait;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/user/auth", name="app_user_auth", methods={"POST"})
 */
class AuthController
{
    use HandleTrait;

    private MessageBusInterface $messageBus;
    private UserRepository $userRepository;
    private Security $security;

    public function __construct(MessageBusInterface $messageBus, UserRepository $userRepository, Security $security)
    {
        $this->messageBus = $messageBus;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $user = $this->security->getUser();
        $apiToken = $this->getApiToken($user);
        $user = $this->userRepository->getProfileByEmail($user->getUsername());

        return new JsonResponse([
            'data' => [
                'user' => $user,
                'token' => $apiToken,
            ]
        ]);

    }

    private function getApiToken(UserInterface $user): string
    {
        /** @var UserApiToken $result */
        $result = $this->handle(
            new Envelope(new GenerateAuthUserApiToken($user)),
            $this->messageBus
        );

        return $result->getToken();
    }
}
