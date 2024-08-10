<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PasswordMismatchException extends ApiException
{
    public function __construct(
        string $message = "Passwords do not match.",
        string $uniqueCode = "password_mismatch_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST,
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}