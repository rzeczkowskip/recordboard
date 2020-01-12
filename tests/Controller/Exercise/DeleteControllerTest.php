<?php
namespace App\Tests\Controller\Exercise;

use App\Entity\Exercise;
use App\Test\WebTestCase;

/**
 * @group func
 */
class DeleteControllerTest extends WebTestCase
{
    public function testDeleteExistingExercise(): void
    {
        $client = static::createClient();
        $existingExerciseId = $this->getExercise()->getId();

        $client->xmlHttpRequest(
            'DELETE',
            '/api/v1/exercises/'.$existingExerciseId,
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken(),
            ],
        );

        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());

        $resultAfterDelete = self::$container->get('doctrine')
            ->getManager()
            ->createQuery('SELECT COUNT(e) FROM App:Exercise e WHERE e.id = :id')
            ->setParameter('id', $existingExerciseId)
            ->getSingleScalarResult();

        static::assertEquals(0, $resultAfterDelete);
    }

    public function testDeleteExerciseNotFound(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'DELETE',
            '/api/v1/exercises/invalid-id',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken(),
            ],
            );

        $response = $client->getResponse();
        static::assertEquals(404, $response->getStatusCode());
    }

    public function textDeleteExerciseOfOtherUserFails(): void
    {
        $client = static::createClient();
        $existingExerciseId = $this->getExercise()->getId();

        $client->xmlHttpRequest(
            'DELETE',
            '/api/v1/exercises/'.$existingExerciseId,
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->getUserApiToken('seconduser@example.com'),
            ],
            );

        $response = $client->getResponse();
        static::assertEquals(404, $response->getStatusCode());
    }

    private function getExercise(): Exercise
    {
        return self::$container->get('doctrine')
            ->getManager()
            ->createQuery('SELECT e FROM App:Exercise e')
            ->getSingleResult();
    }
}
