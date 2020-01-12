<?php
namespace App\Model\User;

class User
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
}
