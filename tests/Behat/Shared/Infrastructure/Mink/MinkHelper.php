<?php

declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Mink;

use Behat\Mink\Session;

final class MinkHelper
{
    public function __construct(private readonly Session $session)
    {
    }

    public function sendRequest(string $method, string $url, array $headers = [], string $body = ''): void
    {
        $this->session->getDriver()->getClient()->request(
            $method,
            $url,
            [],
            [],
            $headers,
            $body
        );
    }

    public function sendMultipartRequest(
        string $method,
        string $url,
        array $parameters,
        array $files,
        array $headers = []
    ): void {
        $this->session->getDriver()->getClient()->request(
            $method,
            $url,
            $parameters,
            $files,
            $headers
        );
    }

    public function getResponse(): string
    {
        return $this->session->getPage()->getContent();
    }

    public function getResponseHeaders(): array
    {
        return $this->session->getResponseHeaders();
    }
}
