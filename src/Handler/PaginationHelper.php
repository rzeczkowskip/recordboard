<?php
namespace App\Handler;

use App\Exception\PaginationException;

class PaginationHelper
{
    private int $page;
    private int $pages;
    private int $itemsPerPage;
    private int $totalItems;

    public function __construct(int $totalItems, int $page = 1, int $itemsPerPage = 20)
    {
        if ($itemsPerPage < 1) {
            throw PaginationException::negativePerPageCount();
        }

        if ($page < 1) {
            throw PaginationException::negativePageNumber();
        }

        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
        $this->pages = (int)ceil($totalItems / $this->itemsPerPage);

        if ($page > $this->pages && $this->pages > 0) {
            throw PaginationException::invalidPageNumber($page, $this->pages);
        }
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getQueryOffset(): int
    {
        return ($this->page * $this->itemsPerPage) - $this->itemsPerPage;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }
}
