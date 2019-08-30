<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class RecordValues extends Constraint
{
    public string $exercise = 'exercise';
    public bool $allowExtraFields = false;
    public bool $allowMissingFields = false;
    public string $extraFieldsMessage = 'This field was not expected.';
    public string $missingFieldsMessage = 'This field is missing.';

    public function getOptions(): array
    {
        return [
            'allowExtraFields' => $this->allowExtraFields,
            'allowMissingFields' => $this->allowMissingFields,
            'extraFieldsMessage' => $this->extraFieldsMessage,
            'missingFieldsMessage' => $this->missingFieldsMessage,
        ];
    }
}
