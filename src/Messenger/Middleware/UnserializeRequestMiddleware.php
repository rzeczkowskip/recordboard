<?php

namespace App\Messenger\Middleware;

use App\Messenger\Middleware\Configuration\ArrayRequestStamp;
use App\Messenger\Middleware\Configuration\SerializedRequestStamp;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UnserializeRequestMiddleware implements MiddlewareInterface
{
    private SerializerInterface $serializer;

    private DenormalizerInterface $denormalizer;

    public function __construct(SerializerInterface $serializer, DenormalizerInterface $denormalizer)
    {
        $this->serializer = $serializer;
        $this->denormalizer = $denormalizer;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        /** @var SerializedRequestStamp|null $requestConfig */
        if ($requestConfig = $envelope->last(SerializedRequestStamp::class)) {
            $subject = $envelope->getMessage();

            $data = $requestConfig->getData();
            $format = $requestConfig->getFormat();

            $context = $requestConfig->getContext();
            $context['object_to_populate'] = $subject;

            try {
                $this->serializer->deserialize(
                    $data,
                    \get_class($subject),
                    $format,
                    $context
                );
            } catch (NotEncodableValueException | ExtraAttributesException $e) {
                throw new BadRequestHttpException($e->getMessage(), $e);
            }
        }

        /** @var ArrayRequestStamp|null $requestConfig */
        if ($requestConfig = $envelope->last(ArrayRequestStamp::class)) {
            $subject = $envelope->getMessage();

            $data = $requestConfig->getData();

            $context = $requestConfig->getContext();
            $context['object_to_populate'] = $subject;

            try {
                $this->denormalizer->denormalize(
                    $data,
                    \get_class($subject),
                    null,
                    $context
                );
            } catch (NotEncodableValueException | ExtraAttributesException $e) {
                throw new BadRequestHttpException($e->getMessage(), $e);
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
