<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Web\User\Domain\User;

interface MessageRepository
{
    public function save(Message $message);

    /**
     * @param Conversation $conversation
     * @return array<Message>
     */
    public function searchMessages(Conversation $conversation): array;
}