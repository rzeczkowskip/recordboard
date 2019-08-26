<?php
namespace App\DTO\Exercise;

use Ramsey\Uuid\UuidInterface;

class Exercise
{
    public UuidInterface $id;
    public string $name;
    public array $attributes;

    public function __construct(UuidInterface $id, string $name, array $attributes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
    }
}
