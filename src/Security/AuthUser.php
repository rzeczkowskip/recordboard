<?php
namespace App\Security;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthUser implements UserInterface
{
    private string $id;
    private string $password;
    private array $roles;

    public function __construct(UuidInterface $id, string $password, array $roles = [])
    {
        $this->id = $id->toString();
        $this->password = $password;

        if (!$roles) {
            $roles = ['ROLE_USER'];
        }

        $this->roles = $roles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->id;
    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): void
    {
    }
}
