<?php

namespace App\Messenger\Middleware\Configuration;

use Symfony\Component\Messenger\Stamp\StampInterface;

class ArrayRequestStamp implements StampInterface
{
    private array $data;
    private array $context;

    public function __construct(array $data, array $context = [])
    {
        $this->data = $data;
        $this->context = $context;
    }

    public function serialize(): string
    {
        return serialize([
            'data' => $this->data,
            'context' => $this->context,
        ]);
    }

    public function unserialize($serialized): void
    {
        [
            'data' => $data,
            'context' => $context,
        ] = unserialize($serialized, ['allowed_classes' => false]);

        $this->__construct($data, $context);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
