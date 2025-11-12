<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Http;

/**
 * Respuesta HTTP inmutable
 */
final readonly class HttpResponse
{
    public function __construct(
        private int $statusCode,
        private array $body,
        private array $headers = []
    ) {
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isError(): bool
    {
        return $this->statusCode >= 400;
    }
}

