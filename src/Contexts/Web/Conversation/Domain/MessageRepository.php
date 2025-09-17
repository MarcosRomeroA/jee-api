<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

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
}