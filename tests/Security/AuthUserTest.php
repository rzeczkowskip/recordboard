<?php
namespace App\Tests\Security;

use App\Security\AuthUser;
use PHPUnit\Framework\TestCase;

class AuthUserTest extends TestCase
{
    public function testIdReturnsValidUuidString(): void
    {
        $id = uuid_v4();
        $user = new AuthUser($id, '');

        $result = $user->getId();

        static::assertIsString($result);
        static::assertEquals($id, $result);
    }

    public function testDefaultRoleIsUser(): void
    {
        $user = new AuthUser(uuid_v4(), '', []);

        static::assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
