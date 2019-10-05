<?php
namespace App\Exception;

class PaginationException extends \Exception
{
    public static function invalidPageNumber(int $page, int $maxPage): self
    {
        $message = sprintf(
            'Invalid page number %d. Page has to be between 1 and %d',
            $page,
            $maxPage
        );

        return new self($message);
    }

    public static function negativePerPageCount(): self
    {
        return new self('Per page count has to be positive number');
    }

    public static function negativePageNumber(): self
    {
        return new self('Page number has to be a positive number');
    }
}
