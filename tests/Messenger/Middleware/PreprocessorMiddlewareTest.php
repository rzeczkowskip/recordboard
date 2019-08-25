<?php

namespace App\Tests\Messenger\Middleware;

use App\Messenger\Middleware\Configuration\PreprocessStamp;
use App\Messenger\Middleware\PreprocessorInterface;
use App\Messenger\Middleware\PreprocessorMiddleware;
use PHPStan\Testing\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class PreprocessorMiddlewareTest extends TestCase
{
    public function testHandleWithInvalidPreprocessor(): void
    {
        $stamp = new PreprocessStamp('invalid');

        $envelope = new Envelope(
            new class {},
            [$stamp]
        );

        $stack = $this->createMock(StackInterface::class);

        $middleware = new PreprocessorMiddleware();
        $middleware
            ->addPreprocessor(new class implements PreprocessorInterface{});

        $this->expectException(\RuntimeException::class);
        $middleware->handle($envelope, $stack);
    }

    public function testHandleWithValidPreprocessor(): void
    {
        $subject = new class {};
        $next = $this->createMock(MiddlewareInterface::class);
        $stack = $this->createMock(StackInterface::class);

        $preprocessor = $this->createPartialMock(
            PreprocessorInterface::class,
            ['__invoke']
        );

        $stamp = new PreprocessStamp(get_class($preprocessor));


        $envelope = new Envelope(
            $subject,
            [$stamp]
        );

        $preprocessor
            ->expects(static::once())
            ->method('__invoke')
            ->with($subject);

        $stack
            ->expects(static::once())
            ->method('next')
            ->willReturn($next);

        $next
            ->expects(static::once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $middleware = new PreprocessorMiddleware();
        $middleware->addPreprocessor($preprocessor);

        $result = $middleware->handle($envelope, $stack);

        static::assertEquals($envelope, $result);
    }
}
