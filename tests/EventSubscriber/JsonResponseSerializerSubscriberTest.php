<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\JsonResponseSerializerSubscriber;
use App\EventSubscriber\ValidationExceptionSubscriber;
use App\Exception\ValidationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class JsonResponseSerializerSubscriberTest extends KernelTestCase
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
        $events = JsonResponseSerializerSubscriber::getSubscribedEvents();
        $expected = [
            'kernel.view' => 'serializeJsonResponse',
        ];

        static::assertEquals($expected, $events);

        foreach ($events as $method) {
            static::assertTrue(
                method_exists(JsonResponseSerializerSubscriber::class, $method),
                sprintf(
                    'Missing subscribed event method %s',
                    $method
                )
            );
        }
    }

    public function testSerializeWithUnsupportedResponse(): void
    {
        $event = $this->getViewEvent(new class {});

        $this->serializer
            ->expects(static::never())
            ->method('serialize');

        $subscriber = new JsonResponseSerializerSubscriber($this->serializer);
        $subscriber->serializeJsonResponse($event);
    }

    public function testSerialize(): void
    {
        $data = ['test' => 'data'];
        $statusCode = 202;
        $headers = ['test' => 'test'];
        $context = ['serializer' => 'context'];
        $dataJson = json_encode($data);

        $controllerResult = new \App\Http\JsonResponse($data, $statusCode, $headers, $context);

        $event = $this->getViewEvent($controllerResult);

        $this->serializer
            ->expects(static::once())
            ->method('serialize')
            ->with(
                $data,
                'json',
                $context
            )
            ->willReturn($dataJson);

        $subscriber = new JsonResponseSerializerSubscriber($this->serializer);
        $subscriber->serializeJsonResponse($event);

        $response = $event->getResponse();
        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertEquals($dataJson, $response->getContent());
        static::assertEquals($statusCode, $response->getStatusCode());
        static::assertEquals(array_keys(array_intersect_key($response->headers->all(), $headers)), array_keys($headers));
    }

    private function getViewEvent($controllerResult): ViewEvent
    {
        return new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            0,
            $controllerResult
        );
    }
}
