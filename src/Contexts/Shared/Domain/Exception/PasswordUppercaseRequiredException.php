<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class PasswordUppercaseRequiredException extends ApiException
{
    public function __construct(
        int $minUppercase,
        string $message = "Password minimum uppercase letter <%d> required.",
        string $uniqueCode = "password_uppercase_required_exception",
        string $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        $message = sprintf($message, $minUppercase);
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}