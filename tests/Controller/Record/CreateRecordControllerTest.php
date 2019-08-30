<?php
namespace App\Tests\Controller\Record;

use App\Entity\Exercise;
use App\Entity\User;
use App\Entity\UserApiToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group func
 */
class CreateRecordControllerTest extends WebTestCase
{
    private string $token;
    private \App\DTO\Exercise\Exercise $exercise;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $token = new UserApiToken($user);

        $em->persist($token);
        $em->flush();

        $this->exercise = $em->getRepository(Exercise::class)->getExercisesList()[0];
        $this->token = $token->getToken();
    }

    protected function tearDown(): void
    {
        unset($this->token, $this->exercise);
        self::ensureKernelShutdown();
    }

    public function testCreateRecord(): void
    {
        $dataValues = [];
        foreach ($this->exercise->attributes as $attribute) {
            $dataValues[$attribute] = 5;
        }

        $data = [
            'exercise' => $this->exercise->id->toString(),
            'earnedAt' => (new \DateTime('now'))->format('Y-m-d H:i:s'),
            'values' => $dataValues,
        ];

        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            '/api/v1/records',
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$this->token,
            ],
            json_encode($data)
        );

        $response = $client->getResponse();

        static::assertEquals(201, $response->getStatusCode());

    }
}
