<?php
namespace App\Model\User;

class Profile
{
    public string $id;
    public string $email;
    public string $name;

    public function __construct(string $id, string $email, string $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
    }

    public static function fromUser(\App\Entity\User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getName(),
        );
    }
}
