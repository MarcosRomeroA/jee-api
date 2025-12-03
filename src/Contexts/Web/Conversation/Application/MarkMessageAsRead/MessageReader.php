<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\MarkMessageAsRead;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\Exception\UserNotExistsInConversationException;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class MessageReader
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private MessageRepository $messageRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Uuid $conversationId, Uuid $messageId, Uuid $sessionId): void
    {
        $conversation = $this->conversationRepository->findByIdOrFail($conversationId);
        $user = $this->userRepository->findById($sessionId);

        if (!$conversation->containsParticipant($user)) {
            throw new UserNotExistsInConversationException();
        }

        $message = $this->messageRepository->findByIdOrFail($messageId);

        // Only mark as read if the message is from the other participant (not our own message)
        if ($message->getUser()->getId()->value() !== $sessionId->value()) {
            $message->markAsRead();
            $this->entityManager->flush();
        }
    }
}
