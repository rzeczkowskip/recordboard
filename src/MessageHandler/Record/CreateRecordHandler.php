<?php
namespace App\MessageHandler\Record;

use App\Entity\Exercise;
use App\Entity\Record;
use App\Entity\User;
use App\Message\Record\CreateRecord;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateRecordHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateRecord $createRecord): void
    {
        $user = $this->em->getReference(
            User::class,
            $createRecord->getAuthUser()->getId(),
        );

        $exercise = $this->em->getReference(
            Exercise::class,
            Uuid::fromString($createRecord->exercise),
        );

        $record = new Record(
            $user,
            $exercise,
            $createRecord->earnedAt,
            $createRecord->values
        );

        $this->em->persist($record);
        $this->em->flush();
    }
}
