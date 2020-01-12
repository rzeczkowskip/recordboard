<?php
namespace App\Tests\Controller\User;

use App\Test\WebTestCase;

/**
 * @group func
 */
class MyProfileControllerTest extends WebTestCase
{
    public function testGetProfileWithApiKey(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            '/api/v1/user/me',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken(),
            ],
        );

        $response = $client->getResponse();

        static::assertEquals(200, $response->getStatusCode());

        $resultJson = $response->getContent();
        static::assertJson($resultJson);

        $resultArray = json_decode($resultJson, true);
        static::assertArrayHasKey('data', $resultArray);
        static::assertArrayHasKey('id', $resultArray['data']);
        static::assertArrayHasKey('name', $resultArray['data']);
        static::assertArrayHasKey('email', $resultArray['data']);
    }
}
