<?php
namespace App\MessageHandler\User;

use App\Entity\User;
use App\Entity\UserApiToken;
use App\Message\User\GenerateAuthUserApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateAuthUserApiTokenHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GenerateAuthUserApiToken $message): UserApiToken
    {
        /** @var User $user */
        $user = $this->em->getReference(
            User::class,
            Uuid::fromString($message->getUser()->getId())
        );

        $apiToken = new UserApiToken($user);

        $this->em->persist($apiToken);
        $this->em->flush();

        return $apiToken;
    }
}
