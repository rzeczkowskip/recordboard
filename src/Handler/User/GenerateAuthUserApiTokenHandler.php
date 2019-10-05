<?php
namespace App\Handler\User;

use App\Entity\User;
use App\Entity\UserApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

class GenerateAuthUserApiTokenHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generateToken(UuidInterface $user): string
    {
        /** @var User $user */
        $user = $this->em->getReference(
            User::class,
            $user
        );

        $apiToken = new UserApiToken($user);

        $this->em->persist($apiToken);
        $this->em->flush();

        return $apiToken->getToken();
    }
}
