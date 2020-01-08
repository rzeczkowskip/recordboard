<?php
namespace App\Data\Record;

class Record
{
    private string $exercise;
    private \DateTimeInterface $earnedAt;
    private array $values;

    public function __construct(string $exercise, \DateTimeInterface $earnedAt, array $values)
    {
        $this->exercise = $exercise;
        $this->earnedAt = $earnedAt;
        $this->values = $values;
    }

    public function getExercise(): string
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
