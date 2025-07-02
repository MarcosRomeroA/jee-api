<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain\Exception;

use App\Contexts\Shared\Infrastructure\Symfony\ApiException;
use Symfony\Component\HttpFoundation\Response;

class UserNotExistsInConversationException extends ApiException
{
    public function __construct(
        string $message = "User is not a participant of the conversation",
        string $uniqueCode = "user_not_exists_in_conversation",
        int $statusCode = Response::HTTP_NOT_FOUND
    )
    {
        parent::__construct($message, $uniqueCode, $statusCode);
    }
}