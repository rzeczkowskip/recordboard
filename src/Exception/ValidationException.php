<?php
namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException
{
    private ConstraintViolationListInterface $violationList;

    public function __construct(ConstraintViolationListInterface $violationList)
    {
        parent::__construct('Validation failed');
        $this->violationList = $violationList;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
