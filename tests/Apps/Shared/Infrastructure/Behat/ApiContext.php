<?php declare(strict_types=1);

namespace App\Tests\Apps\Shared\Infrastructure\Behat;

use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use JsonException;
use Symfony\Component\DomCrawler\Crawler;
use RuntimeException;

class ApiContext extends RawMinkContext
{
    /**
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo(string $method, string $url): void
    {
        $this->sendRequest($method, $this->locatePath($url));
    }

    /**
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody(string $method, string $url, PyStringNode $body): void
    {
        $this->sendRequestWithPyStringNode($method, $this->locatePath($url), $body);
    }

    /**
     * @Then the response content should be:
     * @throws JsonException
     */
    public function theResponseContentShouldBe(PyStringNode $expectedResponse): void
    {
        $expected = $this->sanitizeOutput($expectedResponse->getRaw());
        $actual = $this->sanitizeOutput($this->getSession()->getPage()->getContent());

        if ($expected === false || $actual === false) {
            throw new RuntimeException('The outputs could not be parsed as JSON');
        }

        if ($expected !== $actual) {
            throw new RuntimeException(
                sprintf("The outputs does not match!\n\n-- Expected:\n%s\n\n-- Actual:\n%s", $expected, $actual)
            );
        }
    }

    /**
     * @Then the response should be empty
     * @throws JsonException
     */
    public function theResponseShouldBeEmpty(): void
    {
        $actual = $this->sanitizeOutput($this->getSession()->getPage()->getContent());

        if (!empty($actual)) {
            throw new RuntimeException(sprintf("The outputs is not empty, Actual:\n%s", $actual));
        }
    }

    // Helpers

    /**
     * @throws JsonException
     */
    private function sanitizeOutput(string $output): false|string
    {
        return json_encode(json_decode(trim($output), true, 512, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR);
    }

    public function sendRequest(string $method, string $url, array $optionalParams = []): Crawler
    {
        $defaultOptionalParams = [
            'parameters' => [],
            'files' => [],
            'server' => ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            'content' => null,
            'changeHistory' => true,
        ];

        $optionalParams = array_merge($defaultOptionalParams, $optionalParams);

        return $this->getSession()->getDriver()->getClient()->request(
            $method,
            $url,
            $optionalParams['parameters'],
            $optionalParams['files'],
            $optionalParams['server'],
            $optionalParams['content'],
            $optionalParams['changeHistory']
        );
    }

    public function sendRequestWithPyStringNode($method, $url, PyStringNode $body): void
    {
        $this->sendRequest($method, $url, ['content' => $body->getRaw()]);
    }
}