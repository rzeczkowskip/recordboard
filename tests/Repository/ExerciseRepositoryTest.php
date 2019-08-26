<?php
namespace App\Tests\Repository;

use App\DTO\User\Profile;
use App\Entity\Exercise;
use App\Entity\User;
use App\Entity\UserApiToken;
use App\Repository\ExerciseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExerciseRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ExerciseRepository $repository;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->em->getRepository(Exercise::class);
    }

    protected function tearDown(): void
    {
        unset($this->em, $this->repository);
        self::ensureKernelShutdown();
    }

    public function testGetExercisesList(): void
    {
        $expectedAttributes = ['weight', 'rep'];

        $result = $this->repository->getExercisesList();

        static::assertIsArray($result);
        static::assertCount(1, $result);

        $exercise = reset($result);
        $exerciseAttributes = $exercise->attributes;

        sort($expectedAttributes);
        sort($expectedAttributes);

        static::assertInstanceOf(\App\DTO\Exercise\Exercise::class, $exercise);
        static::assertInstanceOf(UuidInterface::class, $exercise->id);
        static::assertEquals('Deadlift', $exercise->name);
        static::assertEquals($expectedAttributes, $exerciseAttributes);
    }

    /**
     * @return array|\App\DTO\Exercise\Exercise[]
     */
    public function getExercisesList(): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select(sprintf(
            'NEW %s(e.id, e.name, e.attributes)',
            \App\DTO\Exercise\Exercise::class
        ));

        $qb->orderBy('e.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
