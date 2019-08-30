<?php
namespace App\Tests\Controller\Record;

use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Entity\UserApiToken;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class ListRecordsControllerTest extends WebTestCase
{
    private string $token;
    private \App\DTO\Exercise\Exercise $exercise;
    private Exercise $exerciseEntity;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $token = new UserApiToken($user);

        $this->exercise = $em->getRepository(Exercise::class)->getExercisesList()[0];
        $this->exerciseEntity = $em->getReference(Exercise::class, $this->exercise->id);

        $record = new Record($user, $this->exerciseEntity, new \DateTimeImmutable('now'), ['weight' => 100, 'rep' => 1]);

        $em->persist($record);
        $em->persist($token);
        $em->flush();

        $this->token = $token->getToken();
    }

    protected function tearDown(): void
    {
        unset($this->token);
        self::ensureKernelShutdown();
    }

    public function testResponseJson(): void
    {
        $expectedPagination = [
            'page' => 1,
            'pages' => 1,
            'itemsPerPage' => 20,
            'totalItems' => 1,
        ];

        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            '/api/v1/records',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->token,
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
        static::assertIsArray($record['earnedAt']);

        static::assertEquals($expectedPagination, $resultArray['pagination']);
    }

    public function testFilteredRecordsByExercise(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            '/api/v1/records',
            [
                'exercise' => $this->exercise->id->toString(),
            ],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->token,
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
    }
}
