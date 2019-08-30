<?php
namespace App\Tests\Validator\Constraints;

use App\Validator\Constraints\UniqueEntityDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class UniqueEntityDTOTest extends TestCase
{
    public function testThrowExceptionIfEntityOrEntityClassOptionIsMissing(): void
    {
        $this->expectException(MissingOptionsException::class);
        new UniqueEntityDTO();
    }
}
