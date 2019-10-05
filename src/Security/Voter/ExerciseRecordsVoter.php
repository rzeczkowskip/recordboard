<?php

namespace App\Security\Voter;

use App\Entity\Exercise;
use App\Security\AuthUser;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExerciseRecordsVoter extends Voter
{
    public const EXERCISE_RECORDS_LIST = 'EXERCISE_RECORDS_LIST';
    public const EXERCISE_RECORDS_CREATE = 'EXERCISE_RECORDS_CREATE';

    protected function supports($attribute, $subject): bool
    {
        $attributes = [
            self::EXERCISE_RECORDS_LIST,
            self::EXERCISE_RECORDS_CREATE,
        ];

        return in_array($attribute, $attributes, true) &&
            $subject instanceof Exercise;
    }

    /**
     * @param string $attribute
     * @param Exercise $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $authUser = $token->getUser();
        // if the user is anonymous, OR not AuthUser do not grant access
        if (!$authUser instanceof AuthUser) {
            return false;
        }

        return $subject->canUserAccess(Uuid::fromString($authUser->getUsername()));
    }
}
