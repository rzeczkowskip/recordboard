<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"name", "user"})
 * })
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
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="json_array", nullable=false)
     */
    private array $attributes;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;

    public function __construct(User $user, string $name, array $attributes)
    {
        $this->id = Uuid::uuid4();
        $this->user = $user;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function canUserAccess(UuidInterface $id): bool
    {
        return $this->user->getId()->equals($id);
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
