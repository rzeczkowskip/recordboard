<?php
namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse
{
    private array $data;
    private int $statusCode;
    private array $headers;
    private array $context;

    public function __construct(array $data, int $statusCode = Response::HTTP_OK, array $headers = [], array $context = [])
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->context = $context;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
