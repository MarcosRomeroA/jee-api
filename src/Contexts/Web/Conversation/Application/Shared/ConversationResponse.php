<?php

declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\User\Domain\User;

final class ConversationResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $otherUserId = null,
        public readonly ?string $otherUsername = null,
        public readonly ?string $otherFirstname = null,
        public readonly ?string $otherLastname = null,
        public readonly ?string $otherProfileImage = null,
        public readonly ?string $lastMessageText = null,
        public readonly ?string $lastMessageDate = null,
        public readonly int $unreadCount = 0,
    ) {
    }

    public static function fromEntity(
        Conversation $conversation,
        ?User $currentUser = null,
        ?string $cdnBaseUrl = null,
        int $unreadCount = 0,
    ): self {
        $otherUserId = null;
        $otherUsername = null;
        $otherFirstname = null;
        $otherLastname = null;
        $otherProfileImage = null;

        if ($currentUser !== null) {
            $otherParticipant = $conversation->getOtherParticipant($currentUser);
            if ($otherParticipant !== null) {
                $otherUser = $otherParticipant->getUser();
                $otherUserId = $otherUser->getId()->value();
                $otherUsername = $otherUser->getUsername()->value();
                $otherFirstname = $otherUser->getFirstname()->value();
                $otherLastname = $otherUser->getLastname()->value();

                if ($cdnBaseUrl !== null) {
                    $otherProfileImage = $otherUser->getAvatarUrl(128, $cdnBaseUrl);
                }
            }
        }

        $lastMessage = $conversation->getLastMessage();
        $lastMessageText = $lastMessage?->getContent()->value();
        $lastMessageDate = $lastMessage?->getCreatedAt()->value()->format('Y-m-d H:i:s');

        return new self(
            $conversation->getId()->value(),
            $otherUserId,
            $otherUsername,
            $otherFirstname,
            $otherLastname,
            $otherProfileImage,
            $lastMessageText,
            $lastMessageDate,
            $unreadCount,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
