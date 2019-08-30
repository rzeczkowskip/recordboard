<?php
namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationHelper
{
    /**
     * @Assert\Type("int")
     * @Assert\Positive()
     */
    public int $page;

    private int $pages;
    private int $itemsPerPage;
    private int $totalItems = 0;

    public function __construct(int $itemsPerPage, int $page = 1)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->page = $page;
    }

    public function getQueryOffset(): int
    {
        $page = $this->page - 1;

        if ($page < 1) {
            return 0;
        }

        return $page * $this->itemsPerPage;
    }

    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
        $this->pages = (int) ceil($totalItems / $this->itemsPerPage);
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
