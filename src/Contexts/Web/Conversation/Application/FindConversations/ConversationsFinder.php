<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindConversations;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\ConversationsResponse;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class ConversationsFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private ConversationRepository $conversationRepository,
    )
    {
    }

    public function __invoke(Uuid $sessionId): ConversationsResponse
    {
        $sessionUser = $this->userRepository->findById($sessionId);

        $conversations = $this->conversationRepository->searchConversations($sessionUser);

        return new ConversationsResponse($conversations);
    }
}