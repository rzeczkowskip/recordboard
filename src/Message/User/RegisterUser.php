<?php
namespace App\Message\User;

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
    public string $email;

    /**
     * @Assert\NotBlank()
     */
    public string $password;

    /**
     * @Assert\NotBlank()
     */
    public string $name;

    public static function withData(string $email, string $password, string $name): self
    {
        $message = new self();
        $message->email = $email;
        $message->password = $password;
        $message->name = $name;

        return $message;
    }

    public function uniqueFields(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}
