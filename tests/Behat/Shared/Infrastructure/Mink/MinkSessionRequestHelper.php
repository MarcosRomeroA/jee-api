<?php declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Mink;

use Behat\Gherkin\Node\PyStringNode;

final class MinkSessionRequestHelper
{
    private ?string $authToken = null;

    public function __construct(private readonly MinkHelper $helper)
    {
    }

    public function setAuthToken(?string $token): void
    {
        $this->authToken = $token;
    }

    public function sendRequest(string $method, string $url): void
    {
        $headers = [];
        if ($this->authToken) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->authToken;
        }

        $this->helper->sendRequest(
            $method,
            $url,
            $headers,
            ''
        );
    }

    public function sendRequestWithPyStringNode(string $method, string $url, PyStringNode $body): void
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if ($this->authToken) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $this->authToken;
        }

        $this->helper->sendRequest(
            $method,
            $url,
            $headers,
            $body->getRaw()
        );
    }
}

