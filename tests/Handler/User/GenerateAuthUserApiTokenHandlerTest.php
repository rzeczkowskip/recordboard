<?php
namespace App\Tests\Handler\User;

use App\Entity\User;
use App\Entity\UserApiToken;
use App\Handler\User\GenerateAuthUserApiTokenHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GenerateAuthUserApiTokenHandlerTest extends TestCase
{
    public function testGenerateAuthUserApiToken(): void
    {
        $uuid = '97229930-a86d-4905-8cfd-7d2280944801';

        $em = $this->createMock(EntityManagerInterface::class);
        $userReference = $this->createMock(User::class);

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
        $handler->generateToken($uuid);
    }
}
