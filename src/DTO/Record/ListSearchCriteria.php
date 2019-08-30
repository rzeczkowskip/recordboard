<?php
namespace App\DTO\Record;

use App\Entity\Exercise;
use App\Entity\User;
use App\Message\PaginationHelper;

class ListSearchCriteria
{
    public ?Exercise $exercise = null;

    private User $user;
    private int $queryOffset;
    private int $itemsPerPage;

    public function __construct(User $user, PaginationHelper $pagination)
    {
        $this->user = $user;
        $this->queryOffset = $pagination->getQueryOffset();
        $this->itemsPerPage = $pagination->getItemsPerPage();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getQueryOffset(): int
    {
        return $this->queryOffset;
    }

    public function getQueryLimit(): int
    {
        return $this->itemsPerPage;
    }
}
