<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationExpiredException extends ApiException
{
    public function __construct(
        string $message = "Email confirmation token has expired",
        string $uniqueCode = "email_confirmation_expired",
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}

