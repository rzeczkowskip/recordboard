<?php
namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\UserApiToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class MyProfileControllerTest extends WebTestCase
{
    private string $token;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository(User::class);

        $user = $repository->findOneBy(['email' => 'admin@example.com']);
        $token = new UserApiToken($user);

        $em->persist($token);
        $em->flush();

        $this->token = $token->getToken();
    }

    public function testGetProfileWithApiKey(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'GET',
            '/api/v1/user/me',
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
        static::assertArrayHasKey('id', $resultArray['data']);
        static::assertArrayHasKey('name', $resultArray['data']);
        static::assertArrayHasKey('email', $resultArray['data']);
    }
}
