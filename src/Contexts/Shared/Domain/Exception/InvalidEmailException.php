<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class InvalidEmailException extends ApiException
{
    public function __construct(
        string $email,
        string $message = "Invalid Email <%s>",
        string $uniqueCode = "invalid_email_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        $message = sprintf($message, $email);
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}