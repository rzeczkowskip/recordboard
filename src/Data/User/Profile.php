<?php
namespace App\Data\User;

use Ramsey\Uuid\UuidInterface;

class Profile
{
    public UuidInterface $id;
    public string $email;
    public string $name;

    public function __construct(UuidInterface $id, string $email, string $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
    }
}
