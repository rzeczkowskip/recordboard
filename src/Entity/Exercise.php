<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="guid")
     */
    private string $id;

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
        $this->id = uuid_v4();
        $this->user = $user;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function canUserAccess(string $id): bool
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
