<?php
namespace App\Tests\Controller\Record;

use App\Entity\User;
use App\Entity\UserApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class CreateExerciseControllerTest extends WebTestCase
{
    private string $token;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        /** @var EntityManagerInterface $em */
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $user = $em->createQuery('SELECT u FROM App:User u WHERE u.email = :email')
            ->setParameter('email', 'admin@example.com')
            ->getSingleResult();

        $token = new UserApiToken($user);

        $em->persist($token);
        $em->flush();

        $this->token = $token->getToken();
    }

    protected function tearDown(): void
    {
        unset($this->token);
        self::ensureKernelShutdown();
    }

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
                'HTTP_Authorization' => 'Bearer '.$this->token,
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
