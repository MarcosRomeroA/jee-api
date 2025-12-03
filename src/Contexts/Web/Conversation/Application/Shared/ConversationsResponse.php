<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\User\Domain\User;

final class ConversationsResponse extends Response
{
    /**
     * @param array<Conversation> $conversations
     * @param array<string, int> $unreadCounts Map of conversationId => unreadCount
     */
    public function __construct(
        private readonly array $conversations,
        private readonly ?User $currentUser = null,
        private readonly ?FileManager $fileManager = null,
        private readonly array $unreadCounts = [],
    ) {
    }

    public function toArray(): array
    {
        $data = [];
        $totalUnreadMessages = 0;

        foreach ($this->conversations as $conversation) {
            $conversationId = $conversation->getId()->value();
            $unreadCount = $this->unreadCounts[$conversationId] ?? 0;
            $totalUnreadMessages += $unreadCount;

            $data[] = ConversationResponse::fromEntity(
                $conversation,
                $this->currentUser,
                $this->fileManager,
                $unreadCount,
            );
        }

        return [
            'data' => $data,
            'metadata' => [
                'total' => count($data),
                'totalUnreadMessages' => $totalUnreadMessages,
            ],
        ];
    }
}
