<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Record
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    private string $id;

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
        $this->id = uuid_v4();
        $this->exercise = $exercise;
        $this->earnedAt = $earnedAt;
        $this->values = $values;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function getEarnedAt(): \DateTimeInterface
    {
        return $this->earnedAt;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
