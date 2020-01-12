<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ValidationExceptionSubscriber;
use App\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionSubscriberTest extends KernelTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SerializerInterface
     */
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->serializer);
    }

    public function testSubscribedEvents(): void
    {
        $events = ValidationExceptionSubscriber::getSubscribedEvents();
        $expected = [
            'kernel.exception' => 'handleHttpValidationException',
            'console.error' => 'handleConsoleValidationException',
        ];

        static::assertEquals($expected, $events);

        foreach ($events as $method) {
            static::assertTrue(
                method_exists(ValidationExceptionSubscriber::class, $method),
                sprintf(
                    'Missing subscribed event method %s',
                    $method
                )
            );
        }
    }

    public function testHandleHttpValidationExceptionWithInvalidException(): void
    {
        $request = $this->createMock(Request::class);
        $event = $this->getExceptionEvent(new \Exception(), $request);

        $request
            ->expects(static::never())
            ->method('getMethod');

        $subscriber = new ValidationExceptionSubscriber($this->serializer);
        $subscriber->handleHttpValidationException($event);
    }

    /**
     * @dataProvider handleValidationExceptionProvider
     */
    public function testHandleHttpValidationException(string $requestMethod, int $responseStatusCode): void
    {
        $expectedJson = json_encode(['violations' => ['violation']]);
        $format = 'json';

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $exception = $this->createMock(ValidationException::class);

        $exception
            ->expects(static::once())
            ->method('getViolations')
            ->willReturn($violations);

        $request = new Request([], [], ['_format' => $format], [], [], ['REQUEST_METHOD' => $requestMethod]);

        $event = $this->getExceptionEvent($exception, $request);

        $this->serializer
            ->expects(static::once())
            ->method('serialize')
            ->with($violations, $format)
            ->willReturn($expectedJson);

        $subscriber = new ValidationExceptionSubscriber($this->serializer);
        $subscriber->handleHttpValidationException($event);

        $response = $event->getResponse();
        static::assertInstanceOf(Response::class, $response);
        static::assertEquals($expectedJson, $response->getContent());
        static::assertEquals($responseStatusCode, $response->getStatusCode());
    }

    public function handleValidationExceptionProvider(): \Generator
    {
        yield 'post' => [
            'POST',
            422
        ];

        yield 'get' => [
            'GET',
            422
        ];

        yield 'delete' => [
            'DELETE',
            403
        ];
    }

    public function testHandleConsoleValidationExceptionWithInvalidException(): void
    {
        $exception = $this->createMock(NonValidationException::class);

        $event = new ConsoleErrorEvent(
            $this->createMock(InputInterface::class),
            new NullOutput(),
            $exception
        );

        $exception
            ->expects(static::never())
            ->method('getViolations');

        $subscriber = new ValidationExceptionSubscriber($this->serializer);
        $subscriber->handleConsoleValidationException($event);
    }

    public function testHandleConsoleValidationException(): void
    {
        $fp = fopen('php://temp', 'wb');

        $exception = $this->createMock(ValidationException::class);
        $output = new StreamOutput($fp);

        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                'error',
                null,
                [],
                null,
                'property',
                ''
            ),
        ]);

        $event = new ConsoleErrorEvent(
            $this->createMock(InputInterface::class),
            $output,
            $exception
        );

        $exception
            ->expects(static::once())
            ->method('getViolations')
            ->willReturn($violations);

        $subscriber = new ValidationExceptionSubscriber($this->serializer);
        $subscriber->handleConsoleValidationException($event);

        rewind($fp);
        $result = trim((string) stream_get_contents($fp));
        fclose($fp);

        static::assertStringContainsString('[ERROR] Validation failed', $result);
        static::assertStringContainsString('property', $result);
        static::assertStringContainsString('error', $result);
    }

    private function getExceptionEvent(\Throwable $exception, ?Request $request = null): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request ?: $this->createMock(Request::class),
            0,
            $exception
        );
    }
}

class NonValidationException extends \Exception {
    public function getViolations(): void
    {
    }
}
