<?php
namespace App\Tests\Repository;

use App\Model\User\Profile;
use App\Entity\User;
use App\Entity\UserApiToken;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UserRepository $repository;
    private array $existingUser;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = self::$container->get(UserRepository::class);
        $this->existingUser = $this->em
            ->createQuery('SELECT u FROM App:User u WHERE u.email = :email')
            ->setParameter('email', 'admin@example.com')
            ->getSingleResult(Query::HYDRATE_ARRAY);
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->repository, $this->existingUser);
        self::ensureKernelShutdown();
    }

    public function testAuthData(): void
    {
        $result = $this->repository->getAuthData($this->existingUser['email']);

        static::assertCount(2, $result);
        static::assertArrayHasKey('id', $result);
        static::assertArrayHasKey('password', $result);

        static::assertEquals($this->existingUser['id'], $result['id']);
        static::assertEquals($this->existingUser['password'], $result['password']);
    }

    public function testAuthDataThrowsExceptionIfNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->repository->getAuthData('invalid');
    }

    public function testGetUserById(): void
    {
        $result = $this->repository->getUserById($this->existingUser['id']);

        static::assertEquals($this->existingUser['id'], $result->getId());
        static::assertEquals($this->existingUser['name'], $result->getName());
        static::assertEquals($this->existingUser['email'], $result->getEmail());
    }

    public function testGetUserByApiToken(): void
    {
        $token = new UserApiToken($this->em->getReference(User::class, $this->existingUser['id']));
        $this->em->persist($token);
        $this->em->flush();

        $result = $this->repository->getUserByApiToken($token->getToken());

        static::assertEquals($this->existingUser['id'], $result->getId());
        static::assertEquals($this->existingUser['name'], $result->getName());
        static::assertEquals($this->existingUser['email'], $result->getEmail());
    }

    public function testGetUserByApiTokenThrowsExceptionIfNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->repository->getUserByApiToken('invalid');
    }
}
