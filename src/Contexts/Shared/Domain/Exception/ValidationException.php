<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends ApiException
{
    private array $errors;

    public function __construct(
        array $errors,
        string $message = "Validation Errors",
        string $uniqueCode = "validation_exception",
        int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY
    )
    {
        $this->errors = $errors;
        parent::__construct($message, $uniqueCode, $statusCode);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}