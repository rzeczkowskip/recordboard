<?php

namespace App\EventSubscriber;

use App\Http\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class JsonResponseSerializerSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'serializeJsonResponse',
        ];
    }

    public function serializeJsonResponse(ViewEvent $event): void
    {
        /** @var JsonResponse $result */
        if (!($result = $event->getControllerResult()) instanceof \App\Http\JsonResponse) {
            return;
        }

        $data = $result->getData();
        $serializerContext = $result->getContext();
        $status = $result->getStatusCode();
        $headers = $result->getHeaders();

        $resultJson = $this->serializer->serialize(
            $data,
            'json',
            $serializerContext,
            );

        $response = \Symfony\Component\HttpFoundation\JsonResponse::fromJsonString(
            $resultJson,
            $status,
            $headers,
            );

        $event->setResponse($response);
    }
}
