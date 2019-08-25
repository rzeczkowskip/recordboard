<?php
namespace App\Tests\MessageHandler\User;

use App\Entity\User;
use App\Entity\UserApiToken;
use App\Message\User\GenerateAuthUserApiToken;
use App\MessageHandler\User\GenerateAuthUserApiTokenHandler;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GenerateAuthUserApiTokenHandlerTest extends TestCase
{
    public function testGenerateAuthUserApiToken(): void
    {
        $uuid = Uuid::fromString('97229930-a86d-4905-8cfd-7d2280944801');

        $em = $this->createMock(EntityManagerInterface::class);
        $authUser = $this->createMock(AuthUser::class);
        $userReference = $this->createMock(User::class);

        $message = new GenerateAuthUserApiToken($authUser);

        $authUser
            ->expects(static::once())
            ->method('getId')
            ->willReturn($uuid);

        $em
            ->expects(static::once())
            ->method('getReference')
            ->with(User::class, $uuid)
            ->willReturn($userReference);

        $em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(UserApiToken::class));

        $em
            ->expects(static::once())
            ->method('flush');

        $handler = new GenerateAuthUserApiTokenHandler($em);
        $result = $handler($message);

        static::assertInstanceOf(UserApiToken::class, $result);
    }
}
