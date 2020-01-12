<?php
namespace App\Tests\Controller\Record;

use App\Test\WebTestCase;
use Doctrine\ORM\Query;

/**
 * @group func
 */
class CreateRecordControllerTest extends WebTestCase
{
    public function testCreateRecord(): void
    {
        $client = self::createClient();
        $exercise = $this->getExercise();

        $dataValues = [];
        foreach ($exercise['attributes'] as $attribute) {
            $dataValues[$attribute] = 5;
        }

        $now = new \DateTime('now');

        $data = [
            'earnedAt' => $now->format('Y-m-d H:i:s'),
            'values' => $dataValues,
        ];

        $expectedResultData = [
            'exercise' => $exercise['id'],
            'values' => $dataValues,
            'earnedAt' => $now->format('c'),
        ];

        $client->xmlHttpRequest(
            'POST',
            sprintf('/api/v1/exercises/%s/records', $exercise['id']),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken(),
            ],
            json_encode($data)
        );

        $response = $client->getResponse();

        static::assertEquals(201, $response->getStatusCode());

        $resultJson = $response->getContent();
        static::assertJson($resultJson);

        $resultArray = json_decode($resultJson, true);
        static::assertArrayHasKey('data', $resultArray);

        $record = $resultArray['data'];

        static::assertEquals($expectedResultData, $record);
//        static::assertArrayHasKey('exercise', $record);
//        static::assertArrayHasKey('earnedAt', $record);
//        static::assertArrayHasKey('values', $record);
//        static::assertIsArray($record['values']);
//        static::assertIsString($record['earnedAt']);
    }

    public function testExerciseNotFoundReturns404(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
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
            'POST',
            sprintf('/api/v1/exercises/%s/records', $exercise['id']),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer invalid-token',
            ],
            ''
        );

        $response = $client->getResponse();
        static::assertEquals(401, $response->getStatusCode());
    }

    public function testUserCantAccessOtherUserExercise(): void
    {
        $client = static::createClient();
        $exercise = $this->getExercise();

        $client->xmlHttpRequest(
            'POST',
            sprintf('/api/v1/exercises/%s/records', $exercise['id']),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken('second@example.com')
            ],
            '',
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
