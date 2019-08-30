<?php
namespace App\Tests\MessageHandler\Exercise;

use App\Entity\Exercise;
use App\Message\Exercise\CreateExercise;
use App\MessageHandler\Exercise\CreateExerciseHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CreateExerciseHandlerTest extends TestCase
{
    public function testCreateExercise(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);

        $message = CreateExercise::withData('Test', ['weight', 'rep']);

        $em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(Exercise::class));

        $em
            ->expects(static::once())
            ->method('flush');

        $handler = new CreateExerciseHandler($em);
        $handler($message);
    }
}
