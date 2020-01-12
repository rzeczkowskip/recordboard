<?php

namespace App\EventSubscriber;

use App\Exception\ValidationException;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
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
            KernelEvents::EXCEPTION => 'handleHttpValidationException',
            ConsoleEvents::ERROR => 'handleConsoleValidationException'
        ];
    }

    public function handleHttpValidationException(ExceptionEvent $event): void
    {
        /** @var ValidationException $exception */
        if (!($exception = $event->getThrowable()) instanceof ValidationException) {
            return;
        }

        $request = $event->getRequest();

        $responseCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
        if ($request->getMethod() === Request::METHOD_DELETE) {
            $responseCode = JsonResponse::HTTP_FORBIDDEN;
        }

        $violations = $exception->getViolations();

        $violationsJson = $this->serializer->serialize(
            $violations,
            $request->getRequestFormat()
        );

        $response = new Response($violationsJson, $responseCode);
        $response->prepare($request);

        $event->setResponse($response);
    }

    public function handleConsoleValidationException(ConsoleErrorEvent $event): void
    {
        /** @var ValidationException $exception */
        if (!($exception = $event->getError()) instanceof ValidationException) {
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
