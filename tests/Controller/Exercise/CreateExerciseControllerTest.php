<?php
namespace App\Tests\Controller\Record;

use App\Test\WebTestCase;

/**
 * @group func
 */
class CreateExerciseControllerTest extends WebTestCase
{
    public function testCreateExercise(): void
    {
        $data = [
            'name' => 'Test exercise',
            'attributes' => [
                'weight',
                'rep',
            ],
        ];

        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            '/api/v1/exercises',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken()
            ],
            json_encode($data)
        );

        $response = $client->getResponse();
        $resultJson = $response->getContent();

        static::assertEquals(201, $response->getStatusCode());
        static::assertJson($resultJson);

        $resultArray = json_decode($resultJson, true);

        static::assertArrayHasKey('data', $resultArray);

        $exercise = $resultArray['data'];

        static::assertArrayHasKey('id', $exercise);
        static::assertArrayHasKey('name', $exercise);
        static::assertArrayHasKey('attributes', $exercise);
        static::assertIsArray($exercise['attributes']);
    }
}
