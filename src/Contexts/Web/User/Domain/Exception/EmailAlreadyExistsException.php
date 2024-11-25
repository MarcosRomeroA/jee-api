<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class EmailAlreadyExistsException extends ApiException
{
    public function __construct(
        string $message = "Email Already Exists",
        string $uniqueCode = "email_already_exists_exception",
        int $statusCode = Response::HTTP_BAD_REQUEST
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}