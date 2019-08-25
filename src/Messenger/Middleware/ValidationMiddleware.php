<?php

namespace App\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\ValidationMiddleware as BaseMiddleware;
use Symfony\Component\Messenger\Stamp\ValidationStamp;

class ValidationMiddleware extends BaseMiddleware
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->last(ValidationStamp::class) !== null) {
            return parent::handle($envelope, $stack);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
