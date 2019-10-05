<?php
namespace App\DTO\Record;

use App\Entity\Exercise;
use App\Handler\PaginationHelper;

class ListSearchCriteria
{
    private Exercise $exercise;
    private int $queryOffset;
    private int $itemsPerPage;

    public function __construct(Exercise $exercise, PaginationHelper $pagination)
    {
        $this->exercise = $exercise;
        $this->queryOffset = $pagination->getQueryOffset();
        $this->itemsPerPage = $pagination->getItemsPerPage();
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
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
