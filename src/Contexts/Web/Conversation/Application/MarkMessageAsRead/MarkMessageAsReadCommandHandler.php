<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\MarkMessageAsRead;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class MarkMessageAsReadCommandHandler implements CommandHandler
{
    public function __construct(private MessageReader $reader)
    {
    }

    public function __invoke(MarkMessageAsReadCommand $command): void
    {
        $conversationId = new Uuid($command->conversationId);
        $messageId = new Uuid($command->messageId);
        $sessionId = new Uuid($command->sessionId);

        $this->reader->__invoke($conversationId, $messageId, $sessionId);
    }
}
