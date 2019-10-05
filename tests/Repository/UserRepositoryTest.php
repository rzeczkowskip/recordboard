<?php
namespace App\Tests\Repository;

use App\Data\User\Profile;
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

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = self::$container->get(UserRepository::class);
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->repository);
        self::ensureKernelShutdown();
    }

    public function testAuthData(): void
    {
        $result = $this->repository->getAuthData('admin@example.com');

        static::assertCount(2, $result);
        static::assertArrayHasKey('id', $result);
        static::assertArrayHasKey('password', $result);

        static::assertEquals('$argon2id$v=19$m=65536,t=4,p=1$bvEeQQRc3jztvvoslXCJuA$SBW1oAFjSdasyHcJEJApD9VxNY8J39EY/1YQceVvk7s', $result['password']);
        static::assertInstanceOf(UuidInterface::class, $result['id']);
    }

    public function testAuthDataThrowsExceptionIfNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->repository->getAuthData('invalid');
    }

    public function testGetProfileByEmail(): void
    {
        $result = $this->repository->getProfileByEmail('admin@example.com');

        static::assertInstanceOf(Profile::class, $result);
        static::assertEquals('admin@example.com', $result->email);
        static::assertEquals('John Doe', $result->name);
        static::assertInstanceOf(UuidInterface::class, $result->id);
    }

    public function testGetProfileByEmailThrowsExceptionIfNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->repository->getProfileByEmail('invalid');
    }

    public function testGetProfileById(): void
    {
        $id = $this->em
            ->createQuery('SELECT PARTIAL u.{id} FROM App:User u')
            ->setMaxResults(1)
            ->getSingleScalarResult();

        $result = $this->repository->getProfileById($id);

        static::assertInstanceOf(Profile::class, $result);
        static::assertEquals('admin@example.com', $result->email);
        static::assertEquals('John Doe', $result->name);
        static::assertEquals($id, $result->id);
    }

    public function testGetProfileByIdThrowsExceptionIfNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->repository->getProfileById(Uuid::uuid4());
    }

    public function testGetUserByApiToken(): void
    {
        $user = $this->em
            ->createQuery('SELECT u FROM App:User u')
            ->setMaxResults(1)
            ->getSingleResult(Query::HYDRATE_ARRAY);

        $token = new UserApiToken($this->em->getReference(User::class, $user['id']));
        $this->em->persist($token);
        $this->em->flush();

        $result = $this->repository->getUserByApiToken($token->getToken());

        static::assertInstanceOf(\App\Data\User\User::class, $result);
        static::assertEquals($user['email'], $result->email);
        static::assertEquals($user['name'], $result->name);
        static::assertEquals($user['id'], $result->id);
    }

    public function testGetUserByApiTokenThrowsExceptionIfNoResult(): void
    {
        $this->expectException(NoResultException::class);
        $this->repository->getUserByApiToken('invalid');
    }
}
