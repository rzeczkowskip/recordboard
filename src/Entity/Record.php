<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Record
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", nullable=false, unique=true)
     */
    private UuidInterface $id;

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

    public function __construct(Exercise $exercise, \DateTimeInterface $earnedAt, array $values)
    {
        $this->id = Uuid::uuid4();
        $this->exercise = $exercise;
        $this->earnedAt = $earnedAt;
        $this->values = $values;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
