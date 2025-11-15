<?php declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Behat;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\RawMinkContext;
use RuntimeException;

final class ApiContext extends RawMinkContext
{
    private $sessionHelper;
    private $request;
    private ?string $accessToken = null;
    private array $savedVariables = [];

    public function __construct(private readonly Session $minkSession)
    {
        // Instanciar helpers con nombres totalmente cualificados para evitar problemas
        $this->sessionHelper = new \App\Tests\Behat\Shared\Infrastructure\Mink\MinkHelper(
            $this->minkSession,
        );
        $this->request = new \App\Tests\Behat\Shared\Infrastructure\Mink\MinkSessionRequestHelper(
            new \App\Tests\Behat\Shared\Infrastructure\Mink\MinkHelper(
                $minkSession,
            ),
        );
    }

    /**
     * @Given I am authenticated as :email with password :password
     */
    public function iAmAuthenticatedAs(string $email, string $password): void
    {
        $body = json_encode([
            "email" => $email,
            "password" => $password,
        ]);

        $this->sessionHelper->sendRequest(
            "POST",
            $this->locatePath("/api/login"),
            ["CONTENT_TYPE" => "application/json"],
            $body,
        );

        $response = json_decode($this->sessionHelper->getResponse(), true);

        // La respuesta tiene estructura {"data": {"id": "...", "token": "...", ...}}
        if (!isset($response["data"]["token"])) {
            throw new RuntimeException(
                sprintf(
                    "Login failed. Response: %s",
                    $this->sessionHelper->getResponse(),
                ),
            );
        }

        $this->accessToken = $response["data"]["token"];
        $this->request->setAuthToken($this->accessToken);
    }

    /**
     * @Given I am not authenticated
     */
    public function iAmNotAuthenticated(): void
    {
        $this->accessToken = null;
        $this->request->setAuthToken(null);
    }

    /**
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo(string $method, string $url): void
    {
        $this->request->sendRequest($method, $this->locatePath($url));
    }

    /**
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody(
        string $method,
        string $url,
        PyStringNode $body,
    ): void {
        // Replace saved variables in the body
        $bodyString = $this->replaceVariables($body->getRaw());
        $modifiedBody = new PyStringNode([$bodyString], $body->getLine());

        $this->request->sendRequestWithPyStringNode(
            $method,
            $this->locatePath($url),
            $modifiedBody,
        );
    }

    /**
     * Replace variables in the format {variableName} with saved values
     */
    private function replaceVariables(string $text): string
    {
        foreach ($this->savedVariables as $key => $value) {
            $text = str_replace("{" . $key . "}", $value, $text);
        }
        return $text;
    }

    /**
     * @Then the response content should be:
     */
    public function theResponseContentShouldBe(
        PyStringNode $expectedResponse,
    ): void {
        $expected = $this->sanitizeOutput($expectedResponse->getRaw());
        $actual = $this->sanitizeOutput($this->sessionHelper->getResponse());

        if ($expected === false || $actual === false) {
            throw new RuntimeException(
                "The outputs could not be parsed as JSON",
            );
        }

        if ($expected !== $actual) {
            throw new RuntimeException(
                sprintf(
                    "The outputs does not match!\n\n-- Expected:\n%s\n\n-- Actual:\n%s",
                    $expected,
                    $actual,
                ),
            );
        }
    }

    /**
     * @Then the response should be empty
     */
    public function theResponseShouldBeEmpty(): void
    {
        $actual = trim($this->sessionHelper->getResponse());

        if (!empty($actual)) {
            throw new RuntimeException(
                sprintf("The outputs is not empty, Actual:\n%s", $actual),
            );
        }
    }

    /**
     * @Then print last api response
     */
    public function printApiResponse(): void
    {
        print_r($this->sessionHelper->getResponse());
    }

    /**
     * @Then print response headers
     */
    public function printResponseHeaders(): void
    {
        print_r($this->sessionHelper->getResponseHeaders());
    }

    /**
     * @Then the response status code should be :expectedResponseCode
     */
    public function theResponseStatusCodeShouldBe(
        mixed $expectedResponseCode,
    ): void {
        if (
            $this->minkSession->getStatusCode() !== (int) $expectedResponseCode
        ) {
            $responseContent = $this->minkSession->getPage()->getContent();
            throw new RuntimeException(
                sprintf(
                    "The status code <%s> does not match the expected <%s>\nResponse: %s",
                    $this->minkSession->getStatusCode(),
                    $expectedResponseCode,
                    $responseContent,
                ),
            );
        }
    }

    /**
     * @Then the response should contain pagination structure
     */
    public function theResponseShouldContainPaginationStructure(): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        // Verificar que exista el campo "data"
        if (!array_key_exists("data", $response)) {
            throw new RuntimeException(
                sprintf(
                    "Response does not contain required field 'data'. Response: %s",
                    json_encode($response),
                ),
            );
        }

        if (!is_array($response["data"])) {
            throw new RuntimeException('The "data" field must be an array');
        }

        // Verificar que exista el campo "metadata"
        if (!array_key_exists("metadata", $response)) {
            throw new RuntimeException(
                sprintf(
                    "Response does not contain required field 'metadata'. Response: %s",
                    json_encode($response),
                ),
            );
        }

        if (!is_array($response["metadata"])) {
            throw new RuntimeException(
                'The "metadata" field must be an object',
            );
        }

        // Validar campos requeridos en metadata
        $requiredMetadataFields = ["limit", "offset", "total", "count"];
        foreach ($requiredMetadataFields as $field) {
            if (!array_key_exists($field, $response["metadata"])) {
                throw new RuntimeException(
                    sprintf(
                        "Metadata does not contain required field '%s'. Response: %s",
                        $field,
                        json_encode($response),
                    ),
                );
            }
        }

        // Validar tipos de datos
        if (
            !is_int($response["metadata"]["total"]) ||
            $response["metadata"]["total"] < 0
        ) {
            throw new RuntimeException(
                'The "total" field must be a non-negative integer',
            );
        }

        if (
            !is_int($response["metadata"]["count"]) ||
            $response["metadata"]["count"] < 0
        ) {
            throw new RuntimeException(
                'The "count" field must be a non-negative integer',
            );
        }

        if (
            !is_int($response["metadata"]["limit"]) ||
            $response["metadata"]["limit"] < 0
        ) {
            throw new RuntimeException(
                'The "limit" field must be a non-negative integer',
            );
        }

        if (
            !is_int($response["metadata"]["offset"]) ||
            $response["metadata"]["offset"] < 0
        ) {
            throw new RuntimeException(
                'The "offset" field must be a non-negative integer',
            );
        }
    }

    /**
     * @Then the response should have :property property as array
     */
    public function theResponseShouldHavePropertyAsArray(string $property): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!array_key_exists($property, $response)) {
            throw new RuntimeException(
                sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    json_encode($response),
                ),
            );
        }

        if (!is_array($response[$property])) {
            throw new RuntimeException(
                sprintf("The '%s' property must be an array", $property),
            );
        }
    }

    /**
     * @Then the response should have property :property
     */
    public function theResponseShouldHaveProperty(string $property): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        // Si la propiedad está anidada dentro de "data", buscar allí primero
        if (
            array_key_exists("data", $response) &&
            is_array($response["data"])
        ) {
            if (array_key_exists($property, $response["data"])) {
                return;
            }
        }

        // Si no está en "data", buscar en el nivel raíz
        if (!array_key_exists($property, $response)) {
            throw new RuntimeException(
                sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    json_encode($response),
                ),
            );
        }
    }

    /**
     * @Then the response should have :property property
     */
    public function theResponseShouldHavePropertyAlternative(
        string $property,
    ): void {
        // Delegate to the main method
        $this->theResponseShouldHaveProperty($property);
    }

    /**
     * @Then the response should have property :property with value :value
     */
    public function theResponseShouldHavePropertyWithValue(
        string $property,
        string $value,
    ): void {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        $actualValue = null;
        $found = false;

        // Si la propiedad está anidada dentro de "data", buscar allí primero
        if (
            array_key_exists("data", $response) &&
            is_array($response["data"])
        ) {
            if (array_key_exists($property, $response["data"])) {
                $actualValue = is_string($response["data"][$property])
                    ? $response["data"][$property]
                    : json_encode($response["data"][$property]);
                $found = true;
            }
        }

        // Si no está en "data", buscar en el nivel raíz
        if (!$found) {
            if (!array_key_exists($property, $response)) {
                throw new RuntimeException(
                    sprintf(
                        "Response does not contain property '%s'. Response: %s",
                        $property,
                        json_encode($response),
                    ),
                );
            }
            $actualValue = is_string($response[$property])
                ? $response[$property]
                : json_encode($response[$property]);
        }

        if ($actualValue !== $value) {
            throw new RuntimeException(
                sprintf(
                    "The value of property '%s' is '%s', but expected '%s'",
                    $property,
                    $actualValue,
                    $value,
                ),
            );
        }
    }

    /**
     * @Then I save the :property property as :variable
     */
    public function iSaveThePropertyAs(string $property, string $variable): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        $value = null;
        $found = false;

        // Si la propiedad está anidada dentro de "data", buscar allí primero
        if (
            array_key_exists("data", $response) &&
            is_array($response["data"])
        ) {
            if (array_key_exists($property, $response["data"])) {
                $value = $response["data"][$property];
                $found = true;
            }
        }

        // Si no está en "data", buscar en el nivel raíz
        if (!$found && array_key_exists($property, $response)) {
            $value = $response[$property];
            $found = true;
        }

        if (!$found) {
            throw new RuntimeException(
                sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    json_encode($response),
                ),
            );
        }

        // Guardar el valor en una variable de contexto
        $this->savedVariables[$variable] = $value;
    }

    /**
     * @Then the response metadata should have :property property with value :value
     */
    public function theResponseMetadataShouldHavePropertyWithValue(
        string $property,
        string $value,
    ): void {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!array_key_exists("metadata", $response)) {
            throw new RuntimeException(
                sprintf(
                    "Response does not contain 'metadata' field. Response: %s",
                    json_encode($response),
                ),
            );
        }

        if (!is_array($response["metadata"])) {
            throw new RuntimeException(
                'The "metadata" field must be an object',
            );
        }

        if (!array_key_exists($property, $response["metadata"])) {
            throw new RuntimeException(
                sprintf(
                    "Metadata does not contain property '%s'. Response: %s",
                    $property,
                    json_encode($response),
                ),
            );
        }

        $actualValue = is_string($response["metadata"][$property])
            ? $response["metadata"][$property]
            : json_encode($response["metadata"][$property]);

        if ($actualValue !== $value) {
            throw new RuntimeException(
                sprintf(
                    "The value of metadata property '%s' is '%s', but expected '%s'",
                    $property,
                    $actualValue,
                    $value,
                ),
            );
        }
    }

    /**
     * @Then the response should be a valid JSON array
     */
    public function theResponseShouldBeAValidJsonArray(): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException(
                sprintf(
                    "Response is not a valid JSON array. Response: %s",
                    $this->sessionHelper->getResponse(),
                ),
            );
        }

        // Si el array está vacío, es válido
        if (empty($response)) {
            return;
        }

        // Verificar que sea un array indexado (no asociativo)
        if (array_keys($response) !== range(0, count($response) - 1)) {
            throw new RuntimeException(
                "Response is a JSON object, not an array",
            );
        }
    }

    /**
     * @Then the response should have at least :count items
     */
    public function theResponseShouldHaveAtLeastItems(int $count): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON array");
        }

        $actualCount = count($response);

        if ($actualCount < $count) {
            throw new RuntimeException(
                sprintf(
                    "Response has %d items, expected at least %d items",
                    $actualCount,
                    $count,
                ),
            );
        }
    }

    /**
     * @Then the response should have :count items
     */
    public function theResponseShouldHaveItems(int $count): void
    {
        $response = json_decode($this->sessionHelper->getResponse(), true);

        if (!is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON array");
        }

        $actualCount = count($response);

        if ($actualCount !== $count) {
            throw new RuntimeException(
                sprintf(
                    "Response has %d items, expected exactly %d items",
                    $actualCount,
                    $count,
                ),
            );
        }
    }

    private function sanitizeOutput(string $output): false|string
    {
        return json_encode(
            json_decode(trim($output), true, 512, JSON_THROW_ON_ERROR),
            JSON_THROW_ON_ERROR,
        );
    }
}
