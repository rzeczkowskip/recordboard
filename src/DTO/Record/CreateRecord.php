<?php
namespace App\DTO\Record;

use App\Validator\Constraints\ExerciseChoice;
use App\Validator\Constraints\RecordValues;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateRecord
{
    /**
     * @ExerciseChoice()
     */
    public UuidInterface $exercise;

    /**
     * @Assert\Uuid()
     */
    public UuidInterface $user;

    /**
     * @Assert\NotBlank()
     *
     * @var \DateTimeInterface
     */
    public \DateTimeInterface $earnedAt;

    /**
     * @RecordValues()
     */
    public array $values = [];
}
