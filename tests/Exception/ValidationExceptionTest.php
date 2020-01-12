<?php
namespace App\Tests\Exception;

use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionTest extends TestCase
{
    public function testGetViolationsReturnViolationList(): void
    {
        $violations = $this->createMock(ConstraintViolationListInterface::class);

        $exception = new ValidationException($violations);

        static::assertEquals($violations, $exception->getViolations());
    }
}
