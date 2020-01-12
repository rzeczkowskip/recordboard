<?php
namespace App\Model\Exercise;

class Exercise
{
    private string $id;
    private string $name;
    private array $attributes;

    public function __construct(string $id, string $name, array $attributes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public static function fromExercise(\App\Entity\Exercise $exercise): self
    {
        return new self(
            $exercise->getId(),
            $exercise->getName(),
            $exercise->getAttributes()
        );
    }

    public function getId(): string
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
