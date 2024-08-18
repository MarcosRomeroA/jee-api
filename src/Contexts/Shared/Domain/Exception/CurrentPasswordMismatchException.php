<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class CurrentPasswordMismatchException extends ApiException
{
    public function __construct(
        string $message = "Current password do not match.",
        string $uniqueCode = "current_password_mismatch_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST,
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}