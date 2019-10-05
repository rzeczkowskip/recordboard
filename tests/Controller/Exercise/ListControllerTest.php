<?php
namespace App\Tests\Controller\Exercise;

use App\Entity\User;
use App\Entity\UserApiToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class ListControllerTest extends WebTestCase
{
    private string $token;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
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
    }

    public function testGetExercises(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            '/api/v1/exercises',
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
        static::assertIsArray($resultArray['data']);
        static::assertCount(1, $resultArray['data']);

        $exercise = reset($resultArray['data']);

        static::assertArrayHasKey('id', $exercise);
        static::assertArrayHasKey('name', $exercise);
        static::assertArrayHasKey('attributes', $exercise);
        static::assertIsArray($exercise['attributes']);

    }
}
