<?php
namespace App\DTO\Record;

use Ramsey\Uuid\UuidInterface;

class Record
{
    public UuidInterface $exercise;
    public \DateTimeInterface $earnedAt;
    public array $values;

    public function __construct(UuidInterface $exercise, \DateTimeInterface $earnedAt, array $values)
    {
        $this->exercise = $exercise;
        $this->earnedAt = $earnedAt;
        $this->values = $values;
    }
}
