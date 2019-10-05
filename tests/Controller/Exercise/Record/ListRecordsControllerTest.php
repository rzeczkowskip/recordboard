<?php
namespace App\Tests\Controller\Exercise\Record;

use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Entity\UserApiToken;
use App\Repository\ExerciseRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class ListRecordsControllerTest extends WebTestCase
{
    private string $token;
    private UuidInterface $exerciseId;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $token = new UserApiToken($user);

        $exercise = self::$container->get(ExerciseRepository::class)->getExercisesList()[0];
        $this->exerciseId = $exercise->getId();
        $exerciseEntity = $em->getReference(Exercise::class, $this->exerciseId);

        $record = new Record($exerciseEntity, new \DateTimeImmutable('now'), ['weight' => 100, 'rep' => 1]);

        $em->persist($record);
        $em->persist($token);
        $em->flush();

        $this->token = $token->getToken();
    }

    protected function tearDown(): void
    {
        unset($this->token, $this->exerciseId);
        //self::ensureKernelShutdown();
    }

    public function testResponseJson(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', $this->exerciseId->toString()),
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
                'HTTP_Authorization' => 'Bearer '.$this->token,
            ],
        );

        $response = $client->getResponse();

        static::assertEquals(404, $response->getStatusCode());
    }

    public function testUnauthorizedReturns401(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', $this->exerciseId->toString()),
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
        $em = self::$container->get('doctrine')->getManager();

        $user = new User('testexerciseaccess@example.com', '', '');
        $token = new UserApiToken($user);

        $em->persist($user);
        $em->persist($token);
        $em->flush();

        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            sprintf('/api/v1/exercises/%s/records', $this->exerciseId->toString()),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$token->getToken(),
            ],
        );

        $response = $client->getResponse();
        static::assertEquals(404, $response->getStatusCode());
    }
}
