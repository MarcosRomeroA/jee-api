<?php declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Mink;

use Behat\Gherkin\Node\PyStringNode;

final class MinkSessionRequestHelper
{
    public function __construct(private readonly MinkHelper $helper)
    {
    }

    public function sendRequest(string $method, string $url): void
    {
        $this->helper->sendRequest(
            $method,
            $url,
            [],
            ''
        );
    }

    public function sendRequestWithPyStringNode(string $method, string $url, PyStringNode $body): void
    {
        $this->helper->sendRequest(
            $method,
            $url,
            ['CONTENT_TYPE' => 'application/json'],
            $body->getRaw()
        );
    }
}

