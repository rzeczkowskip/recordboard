<?php
namespace App\Message\Exercise;

use App\Validator\Constraints\UniqueEntityDTO;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntityDTO(entityClass="App:Exercise", errorPath="name")
 */
class CreateExercise
{
    /**
     * @Assert\NotBlank()
     */
    public string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"App\Entity\Exercise", "getAllowedAttributes"}, multiple=true, min="1")
     */
    public array $attributes;

    public static function withData(string $name, array $attributes): self
    {
        $message = new self();
        $message->name = $name;
        $message->attributes = $attributes;

        return $message;
    }

    public function uniqueFields(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
