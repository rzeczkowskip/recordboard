<?php
namespace App\Handler\Record;

use App\Model\Record\Record;
use App\DTO\Record\ListSearchCriteria;
use App\Entity\Exercise;
use App\Handler\PaginationHelper;
use App\Repository\RecordRepository;

class ListRecordsHandler
{
    public const ITEMS_PER_PAGE = 20;

    private RecordRepository $recordRepository;

    public function __construct(RecordRepository $recordRepository)
    {
        $this->recordRepository = $recordRepository;
    }

    public function getPaginationHelper(
        Exercise $exercise,
        int $page,
        int $itemsPerPage = self::ITEMS_PER_PAGE
    ): PaginationHelper {
        $totalItems = $this->recordRepository->getRecordsCount($exercise->getId());
        return new PaginationHelper($totalItems, $page, $itemsPerPage);
    }

    /**
     * @param Exercise $exercise
     * @param PaginationHelper $paginationHelper
     *
     * @return array|Record[]
     */
    public function getRecords(Exercise $exercise, PaginationHelper $paginationHelper): array
    {
        $searchCriteria = new ListSearchCriteria($exercise, $paginationHelper);

        return $this->recordRepository->getRecords($searchCriteria);
    }
}
