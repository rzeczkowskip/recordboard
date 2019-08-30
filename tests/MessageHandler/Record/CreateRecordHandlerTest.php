<?php
namespace App\Tests\MessageHandler\Record;

use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Message\Exercise\CreateExercise;
use App\Message\Record\CreateRecord;
use App\MessageHandler\Exercise\CreateExerciseHandler;
use App\MessageHandler\Record\CreateRecordHandler;
use App\Security\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateRecordHandlerTest extends TestCase
{
    public function testCreateRecord(): void
    {
        $authUser = $this->createMock(AuthUser::class);
        $authUserId = Uuid::uuid4();
        $exerciseId = Uuid::uuid4();

        $em = $this->createMock(EntityManagerInterface::class);
        $user = $this->createMock(User::class);
        $exercise = $this->createMock(Exercise::class);

        $message = new CreateRecord($authUser);
        $message->exercise = $exerciseId->toString();
        $message->earnedAt = new \DateTimeImmutable('now');
        $message->values = ['rep' => 1];

        $authUser
            ->expects(static::once())
            ->method('getId')
            ->willReturn($authUserId);

        $em
            ->expects(static::exactly(2))
            ->method('getReference')
            ->withConsecutive(
                [User::class, $authUserId],
                [Exercise::class, $exerciseId]
            )
            ->willReturnOnConsecutiveCalls(
                $user,
                $exercise
            );

        $em
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(Record::class));

        $em
            ->expects(static::once())
            ->method('flush');

        $handler = new CreateRecordHandler($em);
        $handler($message);
    }
}
