<?php

namespace App\Messenger\Middleware;

use App\Messenger\Middleware\Configuration\PreprocessStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class PreprocessorMiddleware implements MiddlewareInterface
{
    private array $preprocessors;

    public function __construct()
    {
        $this->preprocessors = [];
    }

    public function addPreprocessor(PreprocessorInterface $preprocessor): void
    {
        $this->preprocessors[get_class($preprocessor)] = $preprocessor;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $stamps = $envelope->all(PreprocessStamp::class);
        $subject = $envelope->getMessage();

        /** @var PreprocessStamp $stamp */
        foreach ($stamps as $stamp) {
            $preprocessor = $stamp->getPreprocessor();

            if (!array_key_exists($preprocessor, $this->preprocessors)) {
                throw new \RuntimeException(sprintf(
                    'Preprocessor "%s" not found',
                    $preprocessor
                ));
            }

            $this->preprocessors[$preprocessor]($subject);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
