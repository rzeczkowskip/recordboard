<?php

namespace App\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\Stamp\StampInterface;

class SerializedRequestStamp implements StampInterface
{
    private string $data;
    private string $format;
    private array $context;

    public function __construct(string $data, string $format = 'json', array $context = [])
    {
        $this->data = $data;
        $this->format = $format;
        $this->context = $context;
    }

    public function serialize(): string
    {
        return serialize([
            'data' => $this->data,
            'format' => $this->format,
            'context' => $this->context,
        ]);
    }

    public function unserialize($serialized): void
    {
        [
            'data' => $data,
            'format' => $format,
            'context' => $context,
        ] = unserialize($serialized, ['allowed_classes' => false]);

        $this->__construct($data, $format, $context);
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
