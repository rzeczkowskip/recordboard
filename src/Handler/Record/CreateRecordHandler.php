<?php
namespace App\Handler\Record;

use App\DTO\Record\CreateRecord;
use App\Entity\Exercise;
use App\Entity\Record;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateRecordHandler
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function createRecord(CreateRecord $createRecord): UuidInterface
    {
        if (($violations = $this->validator->validate($createRecord)) && $violations->count()) {
            throw new ValidationException($violations);
        }

        $exercise = $this->em->getReference(Exercise::class, $createRecord->exercise);

        $record = new Record(
            $exercise,
            $createRecord->earnedAt,
            $createRecord->values
        );

        $this->em->persist($record);
        $this->em->flush();

        return $record->getId();
    }
}
