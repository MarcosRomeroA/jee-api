<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationNotFoundException extends ApiException
{
    public function __construct(
        string $message = "Email confirmation not found",
        string $uniqueCode = "email_confirmation_not_found",
        int $statusCode = Response::HTTP_NOT_FOUND
    ) {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}

