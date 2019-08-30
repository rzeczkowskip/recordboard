<?php
namespace App\Message\Record;

use App\Security\AuthUser;
use App\Validator\Constraints\ExerciseChoice;
use App\Validator\Constraints\RecordValues;
use Symfony\Component\Validator\Constraints as Assert;

class CreateRecord
{
    private AuthUser $authUser;

    /**
     * @Assert\NotBlank()
     * @ExerciseChoice()
     */
    public ?string $exercise = null;

    /**
     * @Assert\NotBlank()
     * @Assert\DateTime()
     *
     * @var \DateTimeInterface|null
     */
    public ?\DateTimeInterface $earnedAt = null;

    /**
     * @RecordValues()
     */
    public array $values = [];

    public function __construct(AuthUser $authUser)
    {
        $this->authUser = $authUser;
    }

    public function getAuthUser(): AuthUser
    {
        return $this->authUser;
    }
}
