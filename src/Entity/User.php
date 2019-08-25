<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     *
     * @var UuidInterface
     */
    private UuidInterface $id;

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
        $this->id = Uuid::uuid4();
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->apiTokens = new ArrayCollection();
    }

    public function generateApiToken(?\DateTimeInterface $expiresAt = null): UserApiToken
    {
        return new UserApiToken($this, $expiresAt);
    }
}
