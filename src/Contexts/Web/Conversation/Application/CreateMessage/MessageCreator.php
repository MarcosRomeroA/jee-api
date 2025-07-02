<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\CreateMessage;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\MessageResponse;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\Exception\UserNotExistsInConversationException;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\User\Domain\UserRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class MessageCreator
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private UserRepository $userRepository,
        private MessageRepository $repository,
        private HubInterface $hub
    )
    {
    }

    public function __invoke(Uuid $conversationId, Uuid $messageId, Uuid $userId, string $content): void
    {
        $conversation = $this->conversationRepository->find($conversationId);

        $user = $this->userRepository->find($userId);

        if (!$conversation->containsParticipant($user)){
            throw new UserNotExistsInConversationException();
        }

        $message = Message::create($messageId, $conversation, $user, new ContentValue($content));

        $this->repository->save($message);

        $update = new Update(
            $_ENV['APP_URL'].'/conversation/' . $conversationId->value(),
            json_encode((MessageResponse::fromEntity($message, $userId->value()))->toArray())
        );

        $this->hub->publish($update);
    }
}