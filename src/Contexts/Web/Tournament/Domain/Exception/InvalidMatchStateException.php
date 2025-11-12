<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidMatchStateException extends ApiException
{
    public function __construct(
        string $message = "Invalid match state for this operation",
        string $uniqueCode = "invalid_match_state",
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}

