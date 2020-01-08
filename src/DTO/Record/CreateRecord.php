<?php
namespace App\DTO\Record;

use App\Validator\Constraints\ExerciseChoice;
use App\Validator\Constraints\RecordValues;
use Symfony\Component\Validator\Constraints as Assert;

class CreateRecord
{
    /**
     * @ExerciseChoice()
     */
    public string $exercise;

    /**
     * @Assert\Uuid()
     */
    public string $user;

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
