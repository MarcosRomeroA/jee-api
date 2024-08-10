<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class InvalidUuidException extends ApiException
{
    public function __construct(
        string $message = "Invalid Uuid",
        string $uniqueCode = "invalid_uuid_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}