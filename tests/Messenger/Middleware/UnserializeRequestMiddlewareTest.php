<?php

namespace App\Tests\Messenger\Middleware;

use App\Messenger\Middleware\Configuration\ArrayRequestStamp;
use App\Messenger\Middleware\Configuration\SerializedRequestStamp;
use App\Messenger\Middleware\UnserializeRequestMiddleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UnserializeRequestMiddlewareTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|DenormalizerInterface
     */
    private DenormalizerInterface $denormalizer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->serializer, $this->denormalizer);
    }

    public function testHandleSerializedRequest(): void
    {
        $subject = new class {
            public string $attr;
        };
        $subjectClass = get_class($subject);

        $data = json_encode(['attr' => 'testData']);
        $format = 'json';
        $context = [];
        $expectedContext = $context;
        $expectedContext['object_to_populate'] = $subject;

        $stamp = new SerializedRequestStamp($data, $format, $context);

        $envelope = new Envelope(
            $subject,
            [$stamp]
        );

        $this->serializer
            ->expects(static::once())
            ->method('deserialize')
            ->with(
                $data,
                $subjectClass,
                $format,
                $expectedContext
            );

        $middleware = new UnserializeRequestMiddleware($this->serializer, $this->denormalizer);
        $result = $this->handleMiddleware($middleware, $envelope);

        static::assertEquals($envelope, $result);
    }

    public function testHandleArrayRequest(): void
    {
        $subject = new class {
            public string $attr;
        };
        $subjectClass = get_class($subject);

        $data = ['attr' => 'testData'];
        $context = [];
        $expectedContext = $context;
        $expectedContext['object_to_populate'] = $subject;

        $stamp = new ArrayRequestStamp($data, $context);

        $envelope = new Envelope(
            $subject,
            [$stamp]
        );

        $this->denormalizer
            ->expects(static::once())
            ->method('denormalize')
            ->with(
                $data,
                $subjectClass,
                null,
                $expectedContext
            );

        $middleware = new UnserializeRequestMiddleware($this->serializer, $this->denormalizer);
        $result = $this->handleMiddleware($middleware, $envelope);

        static::assertEquals($envelope, $result);
    }

    /**
     * @dataProvider handleWithErrorProvider
     */
    public function testHandleWithError(StampInterface $stamp, \Exception $exception): void
    {
        $stack = $this->createMock(StackInterface::class);

        $envelope = new Envelope(
            new class {},
            [$stamp]
        );

        $this->denormalizer
            ->method('denormalize')
            ->willThrowException($exception);

        $this->serializer
            ->method('deserialize')
            ->willThrowException($exception);

        $stack
            ->expects(static::never())
            ->method('next');

        $middleware = new UnserializeRequestMiddleware($this->serializer, $this->denormalizer);

        $this->expectException(BadRequestHttpException::class);

        $middleware->handle($envelope, $stack);
    }

    public function handleWithErrorProvider(): \Generator
    {
        yield [
            new SerializedRequestStamp(''),
            new NotEncodableValueException(),
        ];

        yield [
            new SerializedRequestStamp(''),
            new ExtraAttributesException([]),
        ];

        yield [
            new ArrayRequestStamp([]),
            new NotEncodableValueException(),
        ];

        yield [
            new ArrayRequestStamp([]),
            new NotEncodableValueException(),
        ];
    }

    private function handleMiddleware(MiddlewareInterface $middleware, Envelope $envelope): Envelope
    {
        $next = $this->createMock(MiddlewareInterface::class);
        $stack = $this->createMock(StackInterface::class);

        $stack
            ->expects(static::once())
            ->method('next')
            ->willReturn($next);

        $next
            ->expects(static::once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        return $middleware->handle($envelope, $stack);
    }
}
