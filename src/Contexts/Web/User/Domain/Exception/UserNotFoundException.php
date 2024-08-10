<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends ApiException
{
    public function __construct(
        string $message = "User Not Found",
        string $uniqueCode = "user_not_found_exception",
        int $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}