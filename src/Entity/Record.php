<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecordRepository")
 */
class Record
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", nullable=false, unique=true)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity="Exercise")
     */
    private Exercise $exercise;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $earnedAt;

    /**
     * @ORM\Column(type="json", name="val")
     */
    private array $values;

    public function __construct(User $user, Exercise $exercise, \DateTimeInterface $earnedAt, array $values)
    {
        $this->id = Uuid::uuid4();
        $this->user = $user;
        $this->exercise = $exercise;
        $this->earnedAt = $earnedAt;
        $this->values = $values;
    }
}
