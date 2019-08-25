<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group func
 */
class AuthControllerTest extends WebTestCase
{
    public function testAuthenticateUser(): void
    {
        $email = 'admin@example.com';
        $password = 'secret123';

        $client = static::createClient();
        $response = $this->makeAuthRequest($client, [
            'email' => $email,
            'password' => $password
        ]);

        static::assertEquals(200, $response->getStatusCode());

        $resultJson = $response->getContent();
        static::assertJson($resultJson);

        $resultArray = json_decode($resultJson, true);
        static::assertArrayHasKey('data', $resultArray);
        static::assertArrayHasKey('user', $resultArray['data']);
        static::assertArrayHasKey('token', $resultArray['data']);

        static::assertSame(64, strlen($resultArray['data']['token']));

        static::assertArrayHasKey('id', $resultArray['data']['user']);
        static::assertArrayHasKey('email', $resultArray['data']['user']);
        static::assertArrayHasKey('name', $resultArray['data']['user']);
    }

    public function testAuthenticateUserInvalidCredentials(): void
    {
        $expected = '{"error":"Invalid credentials."}';

        $client = static::createClient();
        $response = $this->makeAuthRequest($client, [
            'email' => 'invalid',
            'password' => 'invalid',
        ]);

        $resultJson = $response->getContent();
        static::assertEquals(401, $response->getStatusCode());
        static::assertJson($resultJson);
        static::assertJsonStringEqualsJsonString($expected, $resultJson);
    }

    private function makeAuthRequest(KernelBrowser $client, array $data): Response
    {
        $client->xmlHttpRequest(
            'POST',
            '/api/v1/user/auth',
            [],
            [],
            [],
            json_encode($data)
        );

        return $client->getResponse();
    }
}
