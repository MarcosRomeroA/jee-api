<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class MessageNotFoundException extends ApiException
{
    public function __construct(
        string $message = "Message Not Found",
        string $uniqueCode = "message_not_found_exception",
        int $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}
