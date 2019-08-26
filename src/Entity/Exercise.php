<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    public const ATTRIBUTE_WEIGHT = 'weight';
    public const ATTRIBUTE_TIME = 'time';
    public const ATTRIBUTE_REP = 'rep';

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="json_array", nullable=false)
     */
    private array $attributes;

    public function __construct(string $name, array $attributes)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public static function getAllowedAttributes(): array
    {
        return [
            self::ATTRIBUTE_WEIGHT => self::ATTRIBUTE_WEIGHT,
            self::ATTRIBUTE_TIME => self::ATTRIBUTE_TIME,
            self::ATTRIBUTE_REP => self::ATTRIBUTE_REP,
        ];
    }
}
