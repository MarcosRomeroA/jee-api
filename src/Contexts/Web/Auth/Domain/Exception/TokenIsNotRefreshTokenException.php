<?php declare(strict_types=1);

namespace App\Contexts\Web\Auth\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class TokenIsNotRefreshTokenException extends ApiException
{
    public function __construct(
        string $message = "Token is not refresh token",
        string $uniqueCode = "token_is_not_refresh_token_exception",
        int $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}