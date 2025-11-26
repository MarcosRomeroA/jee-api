<?php

declare(strict_types=1);

namespace App\Tests\Behat\Shared\Infrastructure\Behat;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\RawMinkContext;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $body = \json_encode([
            "email" => $email,
            "password" => $password,
        ]);

        $this->sessionHelper->sendRequest(
            "POST",
            $this->locatePath("/api/login"),
            ["CONTENT_TYPE" => "application/json"],
            $body,
        );

        $response = \json_decode($this->sessionHelper->getResponse(), true);

        // La respuesta tiene estructura {"data": {"id": "...", "token": "...", ...}}
        if (!isset($response["data"]["token"])) {
            throw new RuntimeException(
                \sprintf(
                    "Login failed. Response: %s",
                    $this->sessionHelper->getResponse(),
                ),
            );
        }

        $this->accessToken = $response["data"]["token"];
        $this->request->setAuthToken($this->accessToken);
    }

    /**
     * @Given I am authenticated as admin with user :user and password :password
     */
    public function iAmAuthenticatedAsAdmin(string $user, string $password): void
    {
        $body = \json_encode([
            "user" => $user,
            "password" => $password,
        ]);

        $this->sessionHelper->sendRequest(
            "POST",
            $this->locatePath("/api/backoffice/login"),
            ["CONTENT_TYPE" => "application/json"],
            $body,
        );

        $response = \json_decode($this->sessionHelper->getResponse(), true);

        // La respuesta tiene estructura {"data": {"id": "...", "token": "...", ...}}
        if (!isset($response["data"]["token"])) {
            throw new RuntimeException(
                \sprintf(
                    "Admin login failed. Response: %s",
                    $this->sessionHelper->getResponse(),
                ),
            );
        }

        $this->accessToken = $response["data"]["token"];
        $this->request->setAuthToken($this->accessToken);
    }

    /**
     * @Given I am authenticated as user with username :username and password :password
     */
    public function iAmAuthenticatedAsUserWithUsername(string $username, string $password): void
    {
        $body = \json_encode([
            "username" => $username,
            "password" => $password,
        ]);

        $this->sessionHelper->sendRequest(
            "POST",
            $this->locatePath("/api/login"),
            ["CONTENT_TYPE" => "application/json"],
            $body,
        );

        $response = \json_decode($this->sessionHelper->getResponse(), true);

        // La respuesta tiene estructura {"data": {"id": "...", "token": "...", ...}}
        if (!isset($response["data"]["token"])) {
            throw new RuntimeException(
                \sprintf(
                    "Login with username failed. Response: %s",
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
        // Replace saved variables in the URL
        $url = $this->replaceVariables($url);
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
        // Replace saved variables in the URL
        $url = $this->replaceVariables($url);

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
     *
     * @param string $text The text containing variables to replace
     * @return string The text with variables replaced by their saved values
     */
    private function replaceVariables(string $text): string
    {
        foreach ($this->savedVariables as $key => $value) {
            $text = \str_replace('{' . $key . '}', $value, $text);
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
                \sprintf(
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
        $actual = \trim($this->sessionHelper->getResponse());

        if (!empty($actual)) {
            throw new RuntimeException(
                \sprintf("The outputs is not empty, Actual:\n%s", $actual),
            );
        }
    }

    /**
     * @Then print last api response
     */
    public function printApiResponse(): void
    {
        \print_r($this->sessionHelper->getResponse());
    }

    /**
     * @Then print response headers
     */
    public function printResponseHeaders(): void
    {
        \print_r($this->sessionHelper->getResponseHeaders());
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
                \sprintf(
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        // Verificar que exista el campo "data"
        if (!\array_key_exists("data", $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain required field 'data'. Response: %s",
                    \json_encode($response),
                ),
            );
        }

        if (!\is_array($response["data"])) {
            throw new RuntimeException('The "data" field must be an array');
        }

        // Verificar que exista el campo "metadata"
        if (!\array_key_exists("metadata", $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain required field 'metadata'. Response: %s",
                    \json_encode($response),
                ),
            );
        }

        if (!\is_array($response["metadata"])) {
            throw new RuntimeException(
                'The "metadata" field must be an object',
            );
        }

        // Validar campos requeridos en metadata
        $requiredMetadataFields = ["limit", "offset", "total", "count"];
        foreach ($requiredMetadataFields as $field) {
            if (!\array_key_exists($field, $response["metadata"])) {
                throw new RuntimeException(
                    \sprintf(
                        "Metadata does not contain required field '%s'. Response: %s",
                        $field,
                        \json_encode($response),
                    ),
                );
            }
        }

        // Validar tipos de datos
        if (
            !\is_int($response["metadata"]["total"]) ||
            $response["metadata"]["total"] < 0
        ) {
            throw new RuntimeException(
                'The "total" field must be a non-negative integer',
            );
        }

        if (
            !\is_int($response["metadata"]["count"]) ||
            $response["metadata"]["count"] < 0
        ) {
            throw new RuntimeException(
                'The "count" field must be a non-negative integer',
            );
        }

        if (
            !\is_int($response["metadata"]["limit"]) ||
            $response["metadata"]["limit"] < 0
        ) {
            throw new RuntimeException(
                'The "limit" field must be a non-negative integer',
            );
        }

        if (
            !\is_int($response["metadata"]["offset"]) ||
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists($property, $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    \json_encode($response),
                ),
            );
        }

        if (!\is_array($response[$property])) {
            throw new RuntimeException(
                \sprintf("The '%s' property must be an array", $property),
            );
        }
    }

    /**
     * @Then the response should have property :property
     */
    public function theResponseShouldHaveProperty(string $property): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        // Si la propiedad está anidada dentro de "data", buscar allí primero
        if (
            \array_key_exists("data", $response) &&
            \is_array($response["data"])
        ) {
            if (\array_key_exists($property, $response["data"])) {
                return;
            }
        }

        // Si no está en "data", buscar en el nivel raíz
        if (!\array_key_exists($property, $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    \json_encode($response),
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        $actualValue = null;
        $found = false;

        // Si la propiedad está anidada dentro de "data", buscar allí primero
        if (
            \array_key_exists("data", $response) &&
            \is_array($response["data"])
        ) {
            if (\array_key_exists($property, $response["data"])) {
                $actualValue = \is_string($response["data"][$property])
                    ? $response["data"][$property]
                    : \json_encode($response["data"][$property]);
                $found = true;
            }
        }

        // Si no está en "data", buscar en el nivel raíz
        if (!$found) {
            if (!\array_key_exists($property, $response)) {
                throw new RuntimeException(
                    \sprintf(
                        "Response does not contain property '%s'. Response: %s",
                        $property,
                        \json_encode($response),
                    ),
                );
            }
            $actualValue = \is_string($response[$property])
                ? $response[$property]
                : \json_encode($response[$property]);
        }

        if ($actualValue !== $value) {
            throw new RuntimeException(
                \sprintf(
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        $value = null;
        $found = false;

        // Si la propiedad está anidada dentro de "data", buscar allí primero
        if (
            \array_key_exists("data", $response) &&
            \is_array($response["data"])
        ) {
            if (\array_key_exists($property, $response["data"])) {
                $value = $response["data"][$property];
                $found = true;
            }
        }

        // Si no está en "data", buscar en el nivel raíz
        if (!$found && \array_key_exists($property, $response)) {
            $value = $response[$property];
            $found = true;
        }

        if (!$found) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    \json_encode($response),
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists("metadata", $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain 'metadata' field. Response: %s",
                    \json_encode($response),
                ),
            );
        }

        if (!\is_array($response["metadata"])) {
            throw new RuntimeException(
                'The "metadata" field must be an object',
            );
        }

        if (!\array_key_exists($property, $response["metadata"])) {
            throw new RuntimeException(
                \sprintf(
                    "Metadata does not contain property '%s'. Response: %s",
                    $property,
                    \json_encode($response),
                ),
            );
        }

        $actualValue = \is_string($response["metadata"][$property])
            ? $response["metadata"][$property]
            : \json_encode($response["metadata"][$property]);

        if ($actualValue !== $value) {
            throw new RuntimeException(
                \sprintf(
                    "The value of metadata property '%s' is '%s', but expected '%s'",
                    $property,
                    $actualValue,
                    $value,
                ),
            );
        }
    }

    /**
     * @Then the response data should contain user with username :username
     */
    public function theResponseDataShouldContainUserWithUsername(string $username): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists("data", $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain 'data' field. Response: %s",
                    \json_encode($response),
                ),
            );
        }

        if (!\is_array($response["data"])) {
            throw new RuntimeException('The "data" field must be an array');
        }

        // Buscar el usuario con el username especificado
        $found = false;
        foreach ($response["data"] as $user) {
            if (!\is_array($user)) {
                continue;
            }

            if (isset($user["username"]) && $user["username"] === $username) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new RuntimeException(
                \sprintf(
                    "User with username '%s' not found in response data. Response: %s",
                    $username,
                    \json_encode($response["data"]),
                ),
            );
        }
    }

    /**
     * @Then the response should be a valid JSON array
     */
    public function theResponseShouldBeAValidJsonArray(): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException(
                \sprintf(
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
        if (\array_keys($response) !== \range(0, \count($response) - 1)) {
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON array");
        }

        $actualCount = \count($response);

        if ($actualCount < $count) {
            throw new RuntimeException(
                \sprintf(
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
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON array");
        }

        $actualCount = \count($response);

        if ($actualCount !== $count) {
            throw new RuntimeException(
                \sprintf(
                    "Response has %d items, expected exactly %d items",
                    $actualCount,
                    $count,
                ),
            );
        }
    }

    /**
     * @Then the response should be an array
     */
    public function theResponseShouldBeAnArray(): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response is not an array. Response: %s",
                    $this->sessionHelper->getResponse(),
                ),
            );
        }
    }

    /**
     * @Then the response should have a :property property
     */
    public function theResponseShouldHaveAProperty(string $property): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!isset($response[$property])) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not have property '%s'. Response: %s",
                    $property,
                    $this->sessionHelper->getResponse(),
                ),
            );
        }
    }

    /**
     * @Then the :property property should be an array with :count items
     */
    public function thePropertyShouldBeAnArrayWithItems(
        string $property,
        int $count,
    ): void {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!isset($response[$property])) {
            throw new RuntimeException(
                \sprintf("Property '%s' not found in response", $property),
            );
        }

        if (!\is_array($response[$property])) {
            throw new RuntimeException(
                \sprintf("Property '%s' is not an array", $property),
            );
        }

        $actualCount = \count($response[$property]);

        if ($actualCount !== $count) {
            throw new RuntimeException(
                \sprintf(
                    "Property '%s' has %d items, expected exactly %d items",
                    $property,
                    $actualCount,
                    $count,
                ),
            );
        }
    }

    /**
     * @Then the :property property should be an array containing objects with properties :properties
     */
    public function thePropertyShouldBeAnArrayContainingObjectsWithProperties(
        string $property,
        string $properties,
    ): void {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!isset($response[$property])) {
            throw new RuntimeException(
                \sprintf("Property '%s' not found in response", $property),
            );
        }

        if (!\is_array($response[$property]) || empty($response[$property])) {
            throw new RuntimeException(
                \sprintf("Property '%s' is not a non-empty array", $property),
            );
        }

        $expectedProperties = \array_map("trim", \explode(",", $properties));

        foreach ($response[$property] as $index => $item) {
            if (!\is_array($item)) {
                throw new RuntimeException(
                    \sprintf(
                        "Item at index %d in property '%s' is not an object",
                        $index,
                        $property,
                    ),
                );
            }

            foreach ($expectedProperties as $expectedProperty) {
                if (!\array_key_exists($expectedProperty, $item)) {
                    throw new RuntimeException(
                        \sprintf(
                            "Item at index %d in property '%s' does not have property '%s'. Item: %s",
                            $index,
                            $property,
                            $expectedProperty,
                            \json_encode($item),
                        ),
                    );
                }
            }
        }
    }

    /**
     * @Then the JSON response should be:
     */
    public function theJsonResponseShouldBe(PyStringNode $string): void
    {
        $expected = $this->sanitizeOutput($string->getRaw());
        $actual = $this->sanitizeOutput($this->sessionHelper->getResponse());

        if ($expected === false || $actual === false) {
            throw new RuntimeException(
                "The outputs could not be parsed as JSON",
            );
        }

        if ($expected !== $actual) {
            throw new RuntimeException(
                \sprintf(
                    "The JSON responses do not match!\n\n-- Expected:\n%s\n\n-- Actual:\n%s",
                    $expected,
                    $actual,
                ),
            );
        }
    }

    /**
     * @Then the JSON node :node should have :count element
     */
    public function theJsonNodeShouldHaveElement(string $node, int $count): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON");
        }

        $value = $this->getJsonNodeValue($response, $node);

        if (!\is_array($value)) {
            throw new RuntimeException(
                \sprintf("JSON node '%s' is not an array", $node),
            );
        }

        $actualCount = \count($value);

        if ($actualCount !== $count) {
            throw new RuntimeException(
                \sprintf(
                    "JSON node '%s' has %d elements, expected exactly %d element(s)",
                    $node,
                    $actualCount,
                    $count,
                ),
            );
        }
    }

    /**
     * @Then the JSON node :node should have :count elements
     */
    public function theJsonNodeShouldHaveElements(
        string $node,
        int $count,
    ): void {
        // Delegate to singular version
        $this->theJsonNodeShouldHaveElement($node, $count);
    }

    /**
     * @Then the JSON node :node should be equal to :value
     */
    public function theJsonNodeShouldBeEqualTo(
        string $node,
        string $value,
    ): void {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON");
        }

        $actualValue = $this->getJsonNodeValue($response, $node);

        // Convert expected value to proper type
        $expectedValue = $this->convertValue($value);

        if ($actualValue !== $expectedValue) {
            throw new RuntimeException(
                \sprintf(
                    "JSON node '%s' has value '%s', expected '%s'",
                    $node,
                    \json_encode($actualValue),
                    \json_encode($expectedValue),
                ),
            );
        }
    }

    /**
     * @Then the JSON response should have :node with value :value
     */
    public function theJsonResponseShouldHaveWithValue(
        string $node,
        string $value,
    ): void {
        $this->theJsonNodeShouldBeEqualTo($node, $value);
    }

    /**
     * @Then I save the value of JSON node :node as :variable
     */
    public function iSaveTheValueOfJsonNodeAs(
        string $node,
        string $variable,
    ): void {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON");
        }

        $value = $this->getJsonNodeValue($response, $node);

        // Save the value in context variables
        $this->savedVariables[$variable] = $value;
    }

    /**
     * Get a value from JSON response using dot notation (e.g., "data.user.id" or "data[0].name")
     *
     * @param array $data The JSON response data as an associative array
     * @param string $path The path to the desired value using dot notation
     * @return mixed The value found at the specified path
     * @throws RuntimeException If the path is not found in the data
     */
    private function getJsonNodeValue(array $data, string $path): mixed
    {
        $keys = \explode(".", $path);
        $current = $data;

        foreach ($keys as $key) {
            // Handle array access like "data[0]"
            if (\preg_match('/^(.+)\[(\d+)\]$/', $key, $matches)) {
                $arrayKey = $matches[1];
                $index = (int) $matches[2];

                if (!isset($current[$arrayKey])) {
                    throw new RuntimeException(
                        \sprintf(
                            "JSON node '%s' not found in path '%s'",
                            $arrayKey,
                            $path,
                        ),
                    );
                }

                if (!\is_array($current[$arrayKey])) {
                    throw new RuntimeException(
                        \sprintf("JSON node '%s' is not an array", $arrayKey),
                    );
                }

                if (!isset($current[$arrayKey][$index])) {
                    throw new RuntimeException(
                        \sprintf(
                            "Index %d not found in JSON node '%s'",
                            $index,
                            $arrayKey,
                        ),
                    );
                }

                $current = $current[$arrayKey][$index];
            } else {
                if (!isset($current[$key])) {
                    throw new RuntimeException(
                        \sprintf(
                            "JSON node '%s' not found in path '%s'",
                            $key,
                            $path,
                        ),
                    );
                }

                $current = $current[$key];
            }
        }

        return $current;
    }

    /**
     * Convert string value to proper type (e.g., "true" -> true, "123" -> 123)
     *
     * @param string $value The string value to convert
     * @return mixed The converted value (bool, int, float, string, or null)
     */
    private function convertValue(string $value): mixed
    {
        // Handle boolean values
        if ($value === "true") {
            return true;
        }
        if ($value === "false") {
            return false;
        }

        // Handle null
        if ($value === "null") {
            return null;
        }

        // Handle numeric values
        if (\is_numeric($value)) {
            return \str_contains($value, ".") ? (float) $value : (int) $value;
        }

        // Return as string
        return $value;
    }

    /**
     * Sanitize and format JSON output for comparison
     *
     * @param string $output The raw JSON output to sanitize
     * @return false|string The sanitized JSON string or false on failure
     */
    private function sanitizeOutput(string $output): false|string
    {
        return \json_encode(
            \json_decode(\trim($output), true, 512, JSON_THROW_ON_ERROR),
            JSON_THROW_ON_ERROR,
        );
    }

    /**
     * @Then all posts in response should belong to username :username
     */
    public function allPostsInResponseShouldBelongToUsername(string $username): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists("data", $response)) {
            throw new RuntimeException("Response does not contain 'data' field");
        }

        if (!\is_array($response["data"])) {
            throw new RuntimeException('The "data" field must be an array');
        }

        foreach ($response["data"] as $index => $post) {
            if (!\is_array($post)) {
                throw new RuntimeException(
                    \sprintf("Item at index %d is not a valid post object", $index),
                );
            }

            if (!isset($post["username"])) {
                throw new RuntimeException(
                    \sprintf("Post at index %d does not have 'username' field", $index),
                );
            }

            if ($post["username"] !== $username) {
                throw new RuntimeException(
                    \sprintf(
                        "Post at index %d belongs to '%s', expected '%s'",
                        $index,
                        $post["username"],
                        $username,
                    ),
                );
            }
        }
    }

    /**
     * @Then all posts in response should have username containing :substring
     */
    public function allPostsInResponseShouldHaveUsernameContaining(string $substring): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists("data", $response)) {
            throw new RuntimeException("Response does not contain 'data' field");
        }

        if (!\is_array($response["data"])) {
            throw new RuntimeException('The "data" field must be an array');
        }

        foreach ($response["data"] as $index => $post) {
            if (!\is_array($post)) {
                throw new RuntimeException(
                    \sprintf("Item at index %d is not a valid post object", $index),
                );
            }

            if (!isset($post["username"])) {
                throw new RuntimeException(
                    \sprintf("Post at index %d does not have 'username' field", $index),
                );
            }

            if (\stripos($post["username"], $substring) === false) {
                throw new RuntimeException(
                    \sprintf(
                        "Post at index %d has username '%s' which does not contain '%s'",
                        $index,
                        $post["username"],
                        $substring,
                    ),
                );
            }
        }
    }

    /**
     * @Then all posts in response should have body or username containing :substring
     */
    public function allPostsInResponseShouldHaveBodyOrUsernameContaining(string $substring): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists("data", $response)) {
            throw new RuntimeException("Response does not contain 'data' field");
        }

        if (!\is_array($response["data"])) {
            throw new RuntimeException('The "data" field must be an array');
        }

        foreach ($response["data"] as $index => $post) {
            if (!\is_array($post)) {
                throw new RuntimeException(
                    \sprintf("Item at index %d is not a valid post object", $index),
                );
            }

            $bodyContains = isset($post["body"]) && \stripos($post["body"], $substring) !== false;
            $usernameContains = isset($post["username"]) && \stripos($post["username"], $substring) !== false;

            if (!$bodyContains && !$usernameContains) {
                throw new RuntimeException(
                    \sprintf(
                        "Post at index %d has body '%s' and username '%s', neither contains '%s'",
                        $index,
                        $post["body"] ?? "null",
                        $post["username"] ?? "null",
                        $substring,
                    ),
                );
            }
        }
    }

    /**
     * @Then the response should have :property property as empty array
     */
    public function theResponseShouldHavePropertyAsEmptyArray(string $property): void
    {
        $response = \json_decode($this->sessionHelper->getResponse(), true);

        if (!\is_array($response)) {
            throw new RuntimeException("Response is not a valid JSON object");
        }

        if (!\array_key_exists($property, $response)) {
            throw new RuntimeException(
                \sprintf(
                    "Response does not contain property '%s'. Response: %s",
                    $property,
                    \json_encode($response),
                ),
            );
        }

        if (!\is_array($response[$property])) {
            throw new RuntimeException(
                \sprintf("The '%s' property must be an array", $property),
            );
        }

        if (!empty($response[$property])) {
            throw new RuntimeException(
                \sprintf(
                    "The '%s' property is not empty, it contains %d items",
                    $property,
                    \count($response[$property]),
                ),
            );
        }
    }

    /**
     * @Then the response :path should be :value
     */
    public function theResponsePathShouldBe(string $path, string $value): void
    {
        $this->theJsonNodeShouldBeEqualTo($path, $value);
    }

    /**
     * @Then the response should contain a request for team :teamName by player :playerNickname with status :status
     * @deprecated Use theResponseShouldContainARequestForTeamByUserWithStatus instead
     */
    public function theResponseShouldContainARequestForTeamByPlayerWithStatus(
        string $teamName,
        string $playerNickname,
        string $status
    ): void {
        $this->theResponseShouldContainARequestForTeamByUserWithStatus($teamName, $playerNickname, $status);
    }

    /**
     * @Then the response should contain a request for team :teamName by user :userNickname with status :status
     */
    public function theResponseShouldContainARequestForTeamByUserWithStatus(
        string $teamName,
        string $userNickname,
        string $status
    ): void {
        $responseContent = $this->sessionHelper->getResponse();
        $response = \json_decode($responseContent, true);

        if (!isset($response['requests']) || !\is_array($response['requests'])) {
            throw new RuntimeException('Response does not contain a "requests" array');
        }

        $found = false;
        foreach ($response['requests'] as $request) {
            if (
                isset($request['teamName']) &&
                isset($request['userNickname']) &&
                isset($request['status']) &&
                $request['teamName'] === $teamName &&
                $request['userNickname'] === $userNickname &&
                $request['status'] === $status
            ) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new RuntimeException(
                \sprintf(
                    'Could not find a request for team "%s" by user "%s" with status "%s" in the response',
                    $teamName,
                    $userNickname,
                    $status
                )
            );
        }
    }

    /**
     * @Given I send a :method request to :url with file :fileField and parameters:
     */
    public function iSendARequestToWithFileAndParameters(
        string $method,
        string $url,
        string $fileField,
        PyStringNode $body
    ): void {
        $url = $this->replaceVariables($url);
        $bodyString = $this->replaceVariables($body->getRaw());
        $parameters = \json_decode($bodyString, true);

        if (!\is_array($parameters)) {
            throw new RuntimeException("Parameters must be a valid JSON object");
        }

        // Create a temporary test image file
        $tempFile = \tempnam(\sys_get_temp_dir(), 'test_image_');
        $tempFileWithExt = $tempFile . '.png';
        \rename($tempFile, $tempFileWithExt);

        // Create a minimal valid PNG file (1x1 transparent pixel)
        // This is a base64-encoded 1x1 transparent PNG
        $pngData = \base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
        \file_put_contents($tempFileWithExt, $pngData);

        $file = new UploadedFile(
            $tempFileWithExt,
            'test_image.png',
            'image/png',
            null,
            true
        );

        // Parse field name for nested arrays (e.g., "files[0]" => ['files' => [0 => $file]])
        $files = $this->parseFieldToArray($fileField, $file);

        $this->request->sendMultipartRequest(
            $method,
            $this->locatePath($url),
            $parameters,
            $files
        );

        // Clean up temp file after request
        if (\file_exists($tempFileWithExt)) {
            @\unlink($tempFileWithExt);
        }
    }

    /**
     * Parse a field name like "files[0]" into a nested array structure
     */
    private function parseFieldToArray(string $fieldName, mixed $value): array
    {
        // Handle nested array notation like "files[0]"
        if (\preg_match('/^([^\[]+)\[([^\]]*)\]$/', $fieldName, $matches)) {
            $key = $matches[1];
            $index = $matches[2];

            if ($index === '') {
                return [$key => [$value]];
            }

            return [$key => [$index => $value]];
        }

        return [$fieldName => $value];
    }
}
