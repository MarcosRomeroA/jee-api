<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\CreateMessage;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;

readonly class MessageCreator
{
    public function __construct(
        private MessageRepository $messageRepository,
        private ConversationRepository $conversationRepository,
        private UserRepository $userRepository,
        private EventBus $bus,
    ) {
    }

    public function __invoke(
        Uuid $messageId,
        Uuid $conversationId,
        Uuid $userId,
        ContentValue $content
    ): void {
        $conversation = $this->conversationRepository->findByIdOrFail($conversationId);
        $user = $this->userRepository->findById($userId);

        $message = Message::create(
            $messageId,
            $conversation,
            $user,
            $content
        );

        $this->messageRepository->save($message);

        $conversation->updateLastMessage($message);
        $this->conversationRepository->save($conversation);

        $this->bus->publish($message->pullDomainEvents());
    }
}
