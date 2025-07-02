<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Web\User\Domain\User;

interface ConversationRepository
{
    public function searchConversationByParticipantUsers(User $user1, User $user2): ?Conversation;

    /**
     * @param User $user
     * @return array<Conversation>
     */
    public function searchConversations(User $user): array;

    public function save(Conversation $conversation): void;
}