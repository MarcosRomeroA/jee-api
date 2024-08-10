<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class JWTEncodeException extends ApiException
{
    public function __construct(
        string $message = "An error occurred while trying to encode the JWT token.",
        string $uniqueCode = "jwt_encode_failure_exception",
        int $statusCode  = Response::HTTP_INTERNAL_SERVER_ERROR,
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}