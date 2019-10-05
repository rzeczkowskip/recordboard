<?php
namespace App\Tests\Controller\Record;

use App\Entity\Exercise;
use App\Entity\User;
use App\Entity\UserApiToken;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class CreateRecordControllerTest extends WebTestCase
{
    private string $token;
    private string $secondToken;
    private \App\Data\Exercise\Exercise $exercise;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $user = $em->createQuery('SELECT u FROM App:User u WHERE u.email = :email')
            ->setParameter('email', 'admin@example.com')
            ->getSingleResult();
        $token = new UserApiToken($user);

        $secondUser = $em->createQuery('SELECT u FROM App:User u WHERE u.email = :email')
            ->setParameter('email', 'second@example.com')
            ->getSingleResult();
        $secondToken = new UserApiToken($secondUser);

        $em->persist($token);
        $em->persist($secondToken);
        $em->flush();

        $exercise = $em->createQuery('SELECT e FROM App:Exercise e')->getSingleResult(Query::HYDRATE_ARRAY);
        $this->exercise = new \App\Data\Exercise\Exercise($exercise['id'], $exercise['name'], $exercise['attributes']);
        $this->token = $token->getToken();
        $this->secondToken = $secondToken->getToken();
    }

    protected function tearDown(): void
    {
        unset($this->token, $this->exercise);
        self::ensureKernelShutdown();
    }

    public function testCreateRecord(): void
    {
        $dataValues = [];
        foreach ($this->exercise->getAttributes() as $attribute) {
            $dataValues[$attribute] = 5;
        }

        $data = [
            'earnedAt' => (new \DateTime('now'))->format('Y-m-d H:i:s'),
            'values' => $dataValues,
        ];

        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            sprintf('/api/v1/exercises/%s/records', $this->exercise->getId()->toString()),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->token,
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
            'POST',
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
            'POST',
            sprintf('/api/v1/exercises/%s/records', $this->exercise->getId()->toString()),
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
        $em = self::$container->get('doctrine')->getManager();

        $user = new User('testexerciseaccess@example.com', '', '');
        $token = new UserApiToken($user);

        $em->persist($user);
        $em->persist($token);
        $em->flush();

        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            sprintf('/api/v1/exercises/%s/records', $this->exercise->getId()->toString()),
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$token->getToken()
            ],
            '',
        );

        $response = $client->getResponse();
        static::assertEquals(404, $response->getStatusCode());
    }
}
