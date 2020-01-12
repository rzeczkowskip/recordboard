<?php
namespace App\Tests\Repository;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExerciseRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ExerciseRepository $repository;
    private Exercise $existingExercise;
    private string $userId;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = self::$container->get(ExerciseRepository::class);

        $exercise = $this->em->createQuery('SELECT e FROM App:Exercise e')->getSingleResult();
        $this->existingExercise = $exercise;

        $this->userId = $this->em
            ->createQuery('SELECT u.id FROM App:User u WHERE u.email = :email')
            ->setParameter('email', 'admin@example.com')
            ->getSingleScalarResult();
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->repository, $this->existingExercise, $this->userId);
        self::ensureKernelShutdown();
    }

    /**
     * @dataProvider findByIdProvider
     */
    public function testFindById(bool $exists): void
    {
        $result = $this->repository->findById($exists ? $this->existingExercise->getId() : uuid_v4());

        if ($exists) {
            static::assertEquals($result, $this->existingExercise);
        } else {
            static::assertNull($result);
        }
    }

    public function findByIdProvider(): \Generator
    {
        yield 'exercise exists' => [true];
        yield 'exercise doesn\'t exist' => [false];
    }

    /**
     * @dataProvider getListProvider
     */
    public function testGetList(bool $userSpecific): void
    {
        $expectedAttributes = ['weight', 'rep'];

        $result = $this->repository->getList($userSpecific ? $this->userId : null);

        static::assertIsArray($result);
        static::assertCount(1, $result);

        $exercise = reset($result);
        $exerciseAttributes = $exercise->getAttributes();

        sort($expectedAttributes);
        sort($expectedAttributes);

        static::assertInstanceOf(Exercise::class, $exercise);
        static::assertEquals('Deadlift', $exercise->getName());
        static::assertEquals($expectedAttributes, $exerciseAttributes);
    }

    public function getListProvider(): \Generator
    {
        yield 'user specific' => [true];
        yield 'not user specific' => [false];
    }
}
