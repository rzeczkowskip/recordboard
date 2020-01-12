<?php
namespace App\Model\Record;

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

    public static function fromRecord(\App\Entity\Record $record): self
    {
        return new self(
            $record->getExercise()->getId(),
            $record->getEarnedAt(),
            $record->getValues(),
        );
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
