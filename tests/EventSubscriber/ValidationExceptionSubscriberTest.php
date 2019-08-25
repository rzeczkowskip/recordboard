<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ValidationExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionSubscriberTest extends TestCase
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
            'kernel.exception' => 'handleValidationFailedException',
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

    public function testHandleValidationFailedExceptionWithInvalidException(): void
    {
        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event
            ->expects(static::once())
            ->method('getException')
            ->willReturn(new \Exception());

        $event
            ->expects(static::never())
            ->method('getRequest');

        $subscriber = new ValidationExceptionSubscriber($this->serializer);
        $subscriber->handleValidationFailedException($event);
    }

    /**
     * @dataProvider handleValidationFailedExceptionProvider
     */
    public function testHandleValidationFailedException(string $requestMethod, int $responseStatusCode): void
    {
        $expectedJson = json_encode(['violations' => ['violation']]);

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $exception = $this->createMock(ValidationFailedException::class);

        $exception
            ->expects(static::once())
            ->method('getViolations')
            ->willReturn($violations);

        $request = $this->createMock(Request::class);
        $request
            ->expects(static::once())
            ->method('getMethod')
            ->willReturn($requestMethod);

        $this->serializer
            ->expects(static::once())
            ->method('serialize')
            ->with($violations, 'json')
            ->willReturn($expectedJson);

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event
            ->expects(static::once())
            ->method('getException')
            ->willReturn($exception);

        $event
            ->expects(static::once())
            ->method('getRequest')
            ->willReturn($request);

        $event
            ->expects(static::once())
            ->method('setResponse')
            ->with(static::callback(function (JsonResponse $response) use ($expectedJson, $responseStatusCode) {
                return
                    $response instanceof JsonResponse &&
                    $response->getContent() === $expectedJson &&
                    $response->getStatusCode() === $responseStatusCode;
            }));

        $subscriber = new ValidationExceptionSubscriber($this->serializer);
        $subscriber->handleValidationFailedException($event);
    }

    public function handleValidationFailedExceptionProvider(): \Generator
    {
        yield [
            'POST',
            422
        ];

        yield [
            'GET',
            422
        ];

        yield [
            'DELETE',
            403
        ];
    }
}
