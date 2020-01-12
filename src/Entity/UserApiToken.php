<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class UserApiToken
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @var User
     */
    private User $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false, unique=true)
     *
     * @var string
     */
    private string $token;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $generatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $expiresAt;

    public function __construct(User $user, ?\DateTimeInterface $expiresAt = null)
    {
        $this->user = $user;
        $this->generatedAt = new \DateTimeImmutable('now');

        if (!$expiresAt || $expiresAt < $this->generatedAt) {
            $expiresAt = $this->generatedAt->add(new \DateInterval('P30D'));
        }

        $this->expiresAt = $expiresAt;
        $this->token = bin2hex(random_bytes(32));
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
