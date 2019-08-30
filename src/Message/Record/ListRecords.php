<?php
namespace App\Message\Record;

use App\Message\PaginationHelper;
use App\Security\AuthUser;
use App\Validator\Constraints\ExerciseChoice;
use Symfony\Component\Validator\Constraints as Assert;

class ListRecords
{
    public const ITEMS_PER_PAGE = 20;

    private AuthUser $authUser;

    /**
     * @Assert\Uuid()
     * @ExerciseChoice()
     */
    public ?string $exercise = null;

    /**
     * @Assert\Valid()
     */
    public PaginationHelper $pagination;

    public function __construct(AuthUser $authUser, PaginationHelper $pagination)
    {
        $this->authUser = $authUser;
        $this->pagination = $pagination;
    }

    public function getAuthUser(): AuthUser
    {
        return $this->authUser;
    }

    public function getPagination(): PaginationHelper
    {
        return $this->pagination;
    }
}
