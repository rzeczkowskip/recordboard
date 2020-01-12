<?php
namespace App\Tests\Repository;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExerciseRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ExerciseRepository $repository;
    private Exercise $existingExercise;
    private UuidInterface $user;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = self::$container->get(ExerciseRepository::class);

        $exercise = $this->em->createQuery('SELECT e FROM App:Exercise e')->getSingleResult();
        $this->existingExercise = $exercise;

        $id = $this->em
            ->createQuery('SELECT u.id FROM App:User u WHERE u.email = :email')
            ->setParameter('email', 'admin@example.com')
            ->getSingleScalarResult();

        $this->user = Uuid::fromString($id);
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->repository);
        self::ensureKernelShutdown();
    }

    /**
     * @dataProvider findByIdProvider
     */
    public function testFindById(bool $exists): void
    {
        $result = $this->repository->findById($exists ? $this->existingExercise->getId() : Uuid::uuid4());

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
     * @dataProvider getExercisesListProvider
     */
    public function testGetExercisesList(bool $userSpecific): void
    {
        $expectedAttributes = ['weight', 'rep'];

        $result = $this->repository->getExercisesList($userSpecific ? $this->user : null);

        static::assertIsArray($result);
        static::assertCount(1, $result);

        $exercise = reset($result);
        $exerciseAttributes = $exercise->getAttributes();

        sort($expectedAttributes);
        sort($expectedAttributes);

        static::assertInstanceOf(\App\Model\Exercise\Exercise::class, $exercise);
        static::assertInstanceOf(UuidInterface::class, $exercise->getId());
        static::assertEquals('Deadlift', $exercise->getName());
        static::assertEquals($expectedAttributes, $exerciseAttributes);
    }

    public function getExercisesListProvider(): \Generator
    {
        yield 'user specific' => [true];
        yield 'not user specific' => [false];
    }

    public function testGetExerciseData(): void
    {
        $exercise = $this->repository->getExerciseData($this->existingExercise->getId());

        static::assertInstanceOf(\App\Model\Exercise\Exercise::class, $exercise);
        static::assertEquals($exercise->getId(), $this->existingExercise->getId());
    }
}
