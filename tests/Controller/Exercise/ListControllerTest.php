<?php
namespace App\Tests\Controller\Exercise;

use App\Test\WebTestCase;

/**
 * @group func
 */
class ListControllerTest extends WebTestCase
{
    public function testGetExercises(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            '/api/v1/exercises',
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
        static::assertIsArray($resultArray['data']);
        static::assertCount(1, $resultArray['data']);

        $exercise = reset($resultArray['data']);

        static::assertArrayHasKey('id', $exercise);
        static::assertArrayHasKey('name', $exercise);
        static::assertArrayHasKey('attributes', $exercise);
        static::assertIsArray($exercise['attributes']);

    }
}
