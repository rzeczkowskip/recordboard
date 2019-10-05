<?php
namespace App\Data\Record;

use Ramsey\Uuid\UuidInterface;

class Record
{
    private UuidInterface $exercise;
    private \DateTimeInterface $earnedAt;
    private array $values;

    public function __construct(UuidInterface $exercise, \DateTimeInterface $earnedAt, array $values)
    {
        $this->exercise = $exercise;
        $this->earnedAt = $earnedAt;
        $this->values = $values;
    }

    public function getExercise(): UuidInterface
    {
        return $this->exercise;
    }

    public function getEarnedAt(): \DateTimeInterface
    {
        return $this->earnedAt;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
