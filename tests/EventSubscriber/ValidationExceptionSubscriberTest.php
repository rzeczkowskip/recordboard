<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ValidationExceptionSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
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
            'kernel.exception' => 'handleHttpValidationFailedException',
            'console.error' => 'handleConsoleValidationFailedException',
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

    public function testHandleHttpValidationFailedExceptionWithInvalidException(): void
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
        $subscriber->handleHttpValidationFailedException($event);
    }

    /**
     * @dataProvider handleValidationFailedExceptionProvider
     */
    public function testHandleHttpValidationFailedException(string $requestMethod, int $responseStatusCode): void
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
        $subscriber->handleHttpValidationFailedException($event);
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

    public function testHandleConsoleValidationFailedExceptionWithInvalidException(): void
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
        $subscriber->handleConsoleValidationFailedException($event);
    }

    public function testHandleConsoleValidationFailedException(): void
    {
        $expectedOutput = <<<EOT
[ERROR] Validation failed                                                                                              

 ---------- --------- 
  Property   Message  
 ---------- --------- 
  property   error    
 ---------- ---------
EOT;

        $fp = fopen('php://temp', 'wb');

        $exception = $this->createMock(ValidationFailedException::class);
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
        $subscriber->handleConsoleValidationFailedException($event);

        rewind($fp);
        $result = trim((string) stream_get_contents($fp));
        fclose($fp);

        static::assertEquals($expectedOutput, $result);
    }
}

class NonValidationException extends \Exception {
    public function getViolations(): void
    {
    }
}
