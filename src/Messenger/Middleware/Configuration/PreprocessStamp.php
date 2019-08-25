<?php

namespace App\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\Stamp\StampInterface;

class PreprocessStamp implements StampInterface
{
    private string $preprocessor;

    public function __construct(string $preprocessor)
    {
        $this->preprocessor = $preprocessor;
    }

    public function getPreprocessor(): string
    {
        return $this->preprocessor;
    }
}
