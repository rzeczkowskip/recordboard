<?php
namespace App\Tests\DTO;

use App\DTO\Pagination;
use App\Message\PaginationHelper;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function testNamedConstructorFromPaginationHelper(): void
    {
        $itemsPerPage = 10;
        $page = 2;
        $totalItems = 150;
        $pages = 15;

        $helper = new PaginationHelper($itemsPerPage, $page);
        $helper->setTotalItems($totalItems);

        $pagination = Pagination::fromPaginationHelper($helper);

        static::assertEquals($itemsPerPage, $pagination->itemsPerPage);
        static::assertEquals($totalItems, $pagination->totalItems);
        static::assertEquals($page, $pagination->page);
        static::assertEquals($pages, $pagination->pages);
    }
}
