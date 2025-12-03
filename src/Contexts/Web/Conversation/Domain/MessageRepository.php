<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;

interface MessageRepository
{
    public function save(Message $message);

    /**
     * @param Conversation $conversation
     * @return array<Message>
     */
    public function searchMessages(Conversation $conversation): array;

    /**
     * @param Uuid $id
     * @return Message
     */
    public function findByIdOrFail(Uuid $id): Message;

    /**
     * Marks all unread messages from the other participant as read
     *
     * @param Conversation $conversation
     * @param User $reader The user reading the messages (messages from this user won't be marked)
     * @return int Number of messages marked as read
     */
    public function markMessagesAsReadForUser(Conversation $conversation, User $reader): int;

    /**
     * Counts unread messages in a conversation for a specific user
     * (messages sent by others that haven't been read)
     *
     * @param Conversation $conversation
     * @param User $user The user for whom to count unread messages
     * @return int Number of unread messages
     */
    public function countUnreadMessagesForUser(Conversation $conversation, User $user): int;
}
