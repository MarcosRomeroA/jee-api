<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class RankVerificationException extends ApiException
{
    public function __construct(
        string $message = "Error verifying player rank",
        string $uniqueCode = "rank_verification_error",
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $uniqueCode, $statusCode);
    }

    public static function apiNotAvailable(string $apiName): self
    {
        return new self(
            "API $apiName is not available at the moment",
            "api_not_available",
            Response::HTTP_SERVICE_UNAVAILABLE
        );
    }

    public static function playerNotFound(string $username): self
    {
        return new self(
            "Player $username not found in game API",
            "player_not_found_in_api",
            Response::HTTP_NOT_FOUND
        );
    }

    public static function invalidApiKey(): self
    {
        return new self(
            "Invalid API key",
            "invalid_api_key",
            Response::HTTP_UNAUTHORIZED
        );
    }

    public static function rateLimitExceeded(): self
    {
        return new self(
            "Rate limit exceeded, please try again later",
            "rate_limit_exceeded",
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }
}

