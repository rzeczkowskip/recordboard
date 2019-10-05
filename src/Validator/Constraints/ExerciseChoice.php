<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class ExerciseChoice extends Choice
{
    public ?string $user = null;
}
