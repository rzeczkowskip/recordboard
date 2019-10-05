<?php
namespace App\Tests\Handler;

use App\Exception\PaginationException;
use App\Handler\PaginationHelper;
use PHPUnit\Framework\TestCase;

class PaginationHelperTest extends TestCase
{
    /**
     * @dataProvider paginationValuesProvider
     */
    public function testGetPaginationHelperWithInvalidValues(int $page, int $itemsPerPage, int $totalItems, string $expectedMessage): void
    {
        $this->expectException(PaginationException::class);
        $this->expectExceptionMessage($expectedMessage);

        new PaginationHelper($totalItems, $page, $itemsPerPage);
    }

    public function paginationValuesProvider(): \Generator
    {
        yield 'negative items per page' => [
            1,
            -1,
            1,
            'Per page count has to be positive number',
        ];

        yield 'zero items per page' => [
            1,
            0,
            1,
            'Per page count has to be positive number',
        ];

        yield 'page count over page count' => [
            2,
            10,
            1,
            'Invalid page number 2. Page has to be between 1 and 1'
        ];

        yield 'negative page number' => [
            -1,
            1,
            1,
            'Page number has to be a positive number',
        ];
    }
}
