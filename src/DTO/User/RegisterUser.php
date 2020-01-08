<?php
namespace App\DTO\User;

use App\Validator\Constraints\UniqueEntityDTO;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntityDTO(entityClass="App:User", errorPath="email")
 */
class RegisterUser
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email;

    /**
     * @Assert\NotBlank()
     */
    public ?string $password;

    /**
     * @Assert\NotBlank()
     */
    public ?string $name;

    public function uniqueFields(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}
