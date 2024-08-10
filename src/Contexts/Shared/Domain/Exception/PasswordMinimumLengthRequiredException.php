<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PasswordMinimumLengthRequiredException extends ApiException
{
    public function __construct(
        int $length,
        string $message = "Password minimum length %d required",
        string $uniqueCode = "password_minimum_length_required_exception",
        int $statusCode  = Response::HTTP_BAD_REQUEST,
    )
    {
        $message = sprintf($message, $length);
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}