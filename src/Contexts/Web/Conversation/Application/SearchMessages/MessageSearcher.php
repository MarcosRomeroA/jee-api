<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\SearchMessages;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Jwt\MercureJwtGenerator;
use App\Contexts\Web\Conversation\Application\Shared\MessagesResponse;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\Exception\UserNotExistsInConversationException;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class MessageSearcher
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private MessageRepository $messageRepository,
        private UserRepository $userRepository
    )
    {
    }

    public function __invoke(Uuid $userId, Uuid $conversationId): MessagesResponse
    {
        $conversation = $this->conversationRepository->find($conversationId);
        $user = $this->userRepository->find($userId);

        if (!$conversation->containsParticipant($user)){
            throw new UserNotExistsInConversationException();
        }

        $messages = $this->messageRepository->searchMessages($conversation);

        $mercureToken = MercureJwtGenerator::create($_ENV['APP_URL'].'/conversation/'.$conversationId);

        return new MessagesResponse($messages, $mercureToken, $userId->value());
    }
}