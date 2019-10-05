<?php
namespace App\Tests\Security;

use App\Security\AuthUser;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AuthUserTest extends TestCase
{
    public function testIdReturnsValidUuidString(): void
    {
        $id = Uuid::uuid4();
        $user = new AuthUser($id, '');

        $result = $user->getId();

        static::assertIsString($result);
        static::assertTrue(Uuid::isValid($result));
        static::assertSame($id->toString(), $result);
    }

    public function testDefaultRoleIsUser(): void
    {
        $user = new AuthUser(Uuid::uuid4(), '', []);

        static::assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
