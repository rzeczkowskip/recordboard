<?php

namespace App\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

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
            KernelEvents::EXCEPTION => 'handleHttpValidationFailedException',
            ConsoleEvents::ERROR => 'handleConsoleValidationFailedException'
        ];
    }

    public function handleHttpValidationFailedException(GetResponseForExceptionEvent $event): void
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

    public function handleConsoleValidationFailedException(ConsoleErrorEvent $event): void
    {
        /** @var ValidationFailedException $exception */
        if (!($exception = $event->getError()) instanceof ValidationFailedException) {
            return;
        }

        $io = new SymfonyStyle($event->getInput(), $event->getOutput());

        $io->error('Validation failed');

        $violations = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($exception->getViolations() as $violation) {
            $violations[] = [
                $violation->getPropertyPath(),
                $violation->getMessage(),
            ];
        }

        $io->table(
            ['Property', 'Message'],
            $violations
        );
    }
}
