<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\UserApiToken;
use PHPUnit\Framework\TestCase;

class UserApiTokenTest extends TestCase
{
    /**
     * @
     */
    public function testTokenHas32Chars()
    {
        $user = new User('', '', '');

        $apiToken = new UserApiToken($user, new \DateTime('now'));
        $token = $apiToken->getToken();

        static::assertEquals(64, strlen($token));
    }

    public function testTokenHasHexChars()
    {
        $user = new User('', '', '');

        $apiToken = new UserApiToken($user, new \DateTime('now'));
        $token = $apiToken->getToken();

        $match = preg_match('/[a-f0-9]+/', $token);
        static::assertSame(1, $match);
    }
}
