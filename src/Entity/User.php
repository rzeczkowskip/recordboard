<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     *
     * @var string
     */
    private string $id;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     *
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private string $password;

    public function __construct(string $email, string $password, string $name)
    {
        $this->id = uuid_v4();
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
