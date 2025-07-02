<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\CreateMessage;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class CreateMessageCommandHandler implements CommandHandler
{
    public function __construct(
        private MessageCreator $creator
    )
    {
    }

    public function __invoke(CreateMessageCommand $command): void
    {
        $conversationId = new Uuid($command->conversationId);
        $messageId = new Uuid($command->messageId);
        $userId = new Uuid($command->userId);

        $this->creator->__invoke($conversationId, $messageId, $userId, $command->content);
    }
}