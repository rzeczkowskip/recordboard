<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueEntityDTO extends Constraint
{
    public const NOT_UNIQUE_ERROR = '7c39c169-30aa-457c-8f9b-f4157c851c1d';
    public string $message = 'This value is already used.';

    public string $fields = 'uniqueFields';

    public ?string $entity = null;
    public ?string $entityClass = null;

    public ?string $errorPath = null;

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($this->entity === null && $this->entityClass === null) {
            throw new MissingOptionsException(
                'The options "entity" and "entityClass" are both missing. You must set at least one of them',
                ['entity', 'entityClass']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
