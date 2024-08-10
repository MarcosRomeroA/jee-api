<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class ExpiredTokenException extends ApiException
{
    public function __construct(
        string $message = "Expired token.",
        string $uniqueCode = "expired_token_exception",
        int $statusCode  = Response::HTTP_UNAUTHORIZED,
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}