<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class EmailConfirmationResendTooSoonException extends ApiException
{
    public function __construct(
        string $message = "You must wait 24 hours before requesting a new confirmation email",
        string $uniqueCode = "email_confirmation_resend_too_soon",
        int $statusCode = Response::HTTP_TOO_MANY_REQUESTS
    ) {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
