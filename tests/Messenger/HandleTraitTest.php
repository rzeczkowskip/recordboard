<?php

namespace App\Tests\Messenger;

use App\Messenger\HandleTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class HandleTraitTest extends TestCase
{
    public function testThrowsExceptionIfNoHandleStamp(): void
    {
        $envelope = new Envelope(new class {});

        $bus = $this->getMessageBus($envelope);

        $handler = new TestTraitClass($bus);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageRegExp('/Message of type ".+" was handled zero times\. Exactly one handler is expected when using ".+::.+\(\)"\./');

        $handler->run($envelope);
    }

    public function testReturnLastHandledStampIfNoHandlerName(): void
    {
        $stampResult = new class {};
        $handledStamps = [
            new HandledStamp('1', '1'),
            new HandledStamp($stampResult, '2')
        ];
        $lastStamp = end($handledStamps);

        $envelope = new Envelope(
            new class {},
            $handledStamps
        );

        $bus = $this->getMessageBus($envelope);

        $handler = new TestTraitClass($bus);
        $result = $handler->run($envelope);

        static::assertEquals($stampResult, $result);
    }

    public function testReturnHandlerResultWithValidHandlerName(): void
    {
        $stampResult = new class {};
        $name = get_class($stampResult);
        $stamp = new HandledStamp($stampResult, $name);

        $envelope = new Envelope(
            new class {},
            [$stamp]
        );

        $bus = $this->getMessageBus($envelope);

        $handler = new TestTraitClass($bus);
        $result = $handler->run($envelope, $name);

        static::assertEquals($stampResult, $result);
    }

    public function testReturnHandlerResultWithInValidHandlerNameThrowsException(): void
    {
        $stampResult = new class {};
        $name = get_class($stampResult);
        $stamp = new HandledStamp($stampResult, $name);

        $envelope = new Envelope(
            new class {},
            [$stamp]
        );

        $bus = $this->getMessageBus($envelope);

        $handler = new TestTraitClass($bus);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageRegExp('/Message of type ".+" was handled multiple times\. ".+" handler is expected when using ".+::.+\(\)", got .+\./');
        $result = $handler->run($envelope, 'invalid');

        static::assertEquals($stampResult, $result);
    }

    /**
     * @param Envelope $envelope
     *
     * @return MessageBusInterface|MockObject
     */
    private function getMessageBus(Envelope $envelope): MessageBusInterface
    {
        $bus = $this->createMock(MessageBusInterface::class);

        $bus
            ->expects(static::once())
            ->method('dispatch')
            ->with($envelope)
            ->willReturn($envelope);

        return $bus;
    }
}

class TestTraitClass {
    use HandleTrait;

    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function run(Envelope $envelope, ?string $handlerName = null)
    {
        return $this->handle($envelope, $this->messageBus, $handlerName);
    }
}
