<?php
namespace App\Tests\Controller\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class RegisterControllerTest extends WebTestCase
{
    public function testRegisterUser(): void
    {
        $data = [
            'email' => 'janedoe@example.com',
            'password' => 'secret123',
            'name' => 'Jane Doe',
        ];

        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            '/api/v1/user/register',
            [],
            [],
            [],
            json_encode($data)
        );

        $response = $client->getResponse();

        static::assertEquals(201, $response->getStatusCode());
    }
}
