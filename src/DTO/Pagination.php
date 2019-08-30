<?php
namespace App\DTO;

use App\Message\PaginationHelper;

class Pagination
{
    public int $page = 1;
    public int $pages = 1;
    public int $itemsPerPage;
    public int $totalItems = 0;

    public function __construct(int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public static function fromPaginationHelper(PaginationHelper $helper): self
    {
        $pagination = new self($helper->getItemsPerPage());
        $pagination->page = $helper->page;
        $pagination->pages = $helper->getPages();
        $pagination->totalItems = $helper->getTotalItems();

        return $pagination;
    }
}
