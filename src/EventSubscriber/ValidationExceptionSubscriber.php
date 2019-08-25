<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

class ValidationExceptionSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'handleValidationFailedException',
        ];
    }

    public function handleValidationFailedException(GetResponseForExceptionEvent $event): void
    {
        /** @var ValidationFailedException $exception */
        if (!($exception = $event->getException()) instanceof ValidationFailedException) {
            return;
        }

        $responseCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
        if ($event->getRequest()->getMethod() === Request::METHOD_DELETE) {
            $responseCode = JsonResponse::HTTP_FORBIDDEN;
        }

        $event->setResponse(
            JsonResponse::fromJsonString(
                $this->serializer->serialize(
                    $exception->getViolations(),
                    'json'
                ),
                $responseCode
            )
        );
    }
}
