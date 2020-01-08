<?php
namespace App\DTO\Exercise;

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
     * @Assert\Choice(callback={"App\Entity\Exercise", "getAllowedAttributes"}, multiple=true, min="1")
     */
    public array $attributes;

    public string $user;

    public function uniqueFields(): array
    {
        return [
            'name' => $this->name,
            'user' => $this->user,
        ];
    }
}
