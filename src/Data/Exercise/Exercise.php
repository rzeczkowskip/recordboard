<?php
namespace App\Data\Exercise;

use Ramsey\Uuid\UuidInterface;

class Exercise
{
    private UuidInterface $id;
    private string $name;
    private array $attributes;

    public function __construct(UuidInterface $id, string $name, array $attributes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
