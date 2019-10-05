<?php
namespace App\Tests\Data;

use App\Data\Pagination;
use App\Handler\PaginationHelper;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function testNamedConstructorFromPaginationHelper(): void
    {
        $itemsPerPage = 10;
        $page = 2;
        $totalItems = 150;
        $pages = 15;

        $helper = new PaginationHelper($totalItems, $page, $itemsPerPage);
        $pagination = Pagination::fromPaginationHelper($helper);

        static::assertEquals($itemsPerPage, $pagination->itemsPerPage);
        static::assertEquals($totalItems, $pagination->totalItems);
        static::assertEquals($page, $pagination->page);
        static::assertEquals($pages, $pagination->pages);
    }
}
