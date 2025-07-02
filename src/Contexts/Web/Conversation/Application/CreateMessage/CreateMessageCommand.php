<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\CreateMessage;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateMessageCommand implements Command
{
    public function __construct(
        public string $conversationId,
        public string $userId,
        public string $messageId,
        public string $content,
    )
    {
    }
}