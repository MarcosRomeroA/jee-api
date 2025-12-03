<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindConversations;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\ConversationsResponse;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class ConversationsFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private ConversationRepository $conversationRepository,
        private MessageRepository $messageRepository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $sessionId): ConversationsResponse
    {
        $sessionUser = $this->userRepository->findById($sessionId);

        $conversations = $this->conversationRepository->searchConversations($sessionUser);

        // Calculate unread counts for each conversation
        $unreadCounts = [];
        foreach ($conversations as $conversation) {
            $conversationId = $conversation->getId()->value();
            $unreadCounts[$conversationId] = $this->messageRepository->countUnreadMessagesForUser(
                $conversation,
                $sessionUser
            );
        }

        return new ConversationsResponse($conversations, $sessionUser, $this->fileManager, $unreadCounts);
    }
}
