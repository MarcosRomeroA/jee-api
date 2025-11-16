<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

final class EmailAlreadyConfirmedException extends ApiException
{
    public function __construct(
        string $message = "Email already confirmed",
        string $uniqueCode = "email_already_confirmed_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
