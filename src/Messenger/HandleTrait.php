<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * Synchronous message handling based on Symfony\Component\Messenger\HandleTrait by Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
trait HandleTrait
{
    private function handle(Envelope $message, MessageBusInterface $messageBus, ?string $handlerName = null)
    {
        $envelope = $messageBus->dispatch($message);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', \get_class($envelope->getMessage()), \get_class($this), __FUNCTION__));
        }

        if ($handlerName === null) {
            return end($handledStamps)->getResult();
        }

        foreach ($handledStamps as $handledStamp) {
            if ($handledStamp->getHandlerName() === $handlerName) {
                return $handledStamp->getResult();
            }
        }

        $handlers = implode(', ', array_map(static function (HandledStamp $stamp): string {
            return sprintf('"%s"', $stamp->getHandlerName());
        }, $handledStamps));

        throw new LogicException(sprintf(
            'Message of type "%s" was handled multiple times. "%s" handler is expected when using "%s::%s()", got %s.',
            \get_class($envelope->getMessage()),
            $handlerName,
            \get_class($this),
            __FUNCTION__,
            $handlers
        ));
    }
}
