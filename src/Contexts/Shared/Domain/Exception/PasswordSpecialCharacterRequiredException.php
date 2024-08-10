<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PasswordSpecialCharacterRequiredException extends ApiException
{
    public function __construct(
        int $minSpecialChars,
        string $message = "Password minimum special characters <%d> required",
        string $uniqueCode = "password_special_character_required_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        $message = sprintf($message, $minSpecialChars);
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}