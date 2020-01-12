<?php
namespace App\Tests\Controller\Exercise\Record;

use App\Test\WebTestCase;
use Doctrine\ORM\Query;

/**
 * @group func
 */
class ListRecordsControllerTest extends WebTestCase
{
    public function testResponseJson(): void
    {
        $client = static::createClient();
        $exercise = $this->getExercise();

        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', $exercise['id']),
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
        static::assertArrayHasKey('pagination', $resultArray);

        static::assertIsArray($resultArray['data']);
        static::assertCount(1, $resultArray['data']);

        $record = reset($resultArray['data']);

        static::assertArrayHasKey('exercise', $record);
        static::assertArrayHasKey('earnedAt', $record);
        static::assertArrayHasKey('values', $record);
        static::assertIsArray($record['values']);
        static::assertIsString($record['earnedAt']);
    }

    public function testExerciseNotFoundReturns404(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', 'invalid-id'),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken(),
            ],
        );

        $response = $client->getResponse();

        static::assertEquals(404, $response->getStatusCode());
    }

    public function testUnauthorizedReturns401(): void
    {
        $client = static::createClient();
        $exercise = $this->getExercise();

        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', $exercise['id']),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer invalid-token',
            ],
        );

        $response = $client->getResponse();
        static::assertEquals(401, $response->getStatusCode());
    }

    public function testUserCantAccessOtherUserExercise(): void
    {
        $client = static::createClient();
        $exercise = $this->getExercise();

        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', $exercise['id']),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken('second@example.com'),
            ],
        );

        $response = $client->getResponse();
        static::assertEquals(404, $response->getStatusCode());
    }

    private function getExercise(): array
    {
        return self::$container->get('doctrine')
            ->getManager()
            ->createQuery('SELECT e FROM App:Exercise e')
            ->getSingleResult(Query::HYDRATE_ARRAY);
    }
}
