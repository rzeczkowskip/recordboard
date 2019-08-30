<?php
namespace App\Tests\Message;

use App\Message\PaginationHelper;
use PHPUnit\Framework\TestCase;

class PaginationHelperTest extends TestCase
{
    /**
     * @dataProvider setTotalItemsProvider
     */
    public function testSetTotalItems(int $itemsPerPage, int $totalItems, int $expectedPages): void
    {
        $helper = new PaginationHelper($itemsPerPage, 1);
        $helper->setTotalItems($totalItems);

        static::assertEquals($totalItems, $helper->getTotalItems());
        static::assertEquals($expectedPages, $helper->getPages());
        static::assertEquals($itemsPerPage, $helper->getItemsPerPage());
    }

    public function setTotalItemsProvider(): \Generator
    {
        yield [
            25,
            250,
            10
        ];

        yield [
            1,
            1,
            1,
        ];
    }
}
