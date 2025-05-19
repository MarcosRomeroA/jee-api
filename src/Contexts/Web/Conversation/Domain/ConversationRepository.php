<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Domain;

use App\Contexts\Web\User\Domain\User;

interface ConversationRepository
{
    public function searchConversation(User $user1, User $user2): ?Conversation;
}