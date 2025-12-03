<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\MarkMessageAsRead;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class MarkMessageAsReadCommand implements Command
{
    public function __construct(
        public string $conversationId,
        public string $messageId,
        public string $sessionId,
    ) {
    }
}
