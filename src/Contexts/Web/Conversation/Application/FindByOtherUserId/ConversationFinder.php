<?php declare(strict_types=1);

namespace App\Contexts\Web\Conversation\Application\FindByOtherUserId;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\Shared\ConversationResponse;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Participant\Domain\Participant;
use App\Contexts\Web\User\Domain\Exception\OtherUserIsMeException;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class ConversationFinder
{
    public function __construct(
        private UserRepository $userRepository,
        private ConversationRepository $conversationRepository,
    )
    {
    }

    public function __invoke(Uuid $otherUserId, Uuid $sessionId): ConversationResponse
    {
        $otherUser = $this->userRepository->findById($sessionId);

        $sessionUser = $this->userRepository->findById($sessionId);

        if ($otherUserId->equals($sessionId)) {
            throw new OtherUserIsMeException();
        }

        $conversation = $this->conversationRepository->searchConversationByParticipantUsers($otherUser, $sessionUser);

        if (!$conversation){
            $conversation = Conversation::create(Uuid::random());

            $user1 = $this->userRepository->findById($sessionId);
            $participant1 = Participant::create(
                Uuid::random(),
                $conversation,
                $user1,
                true,
            );

            $user2 = $this->userRepository->findById($otherUserId);
            $participant2 = Participant::create(
                Uuid::random(),
                $conversation,
                $user2,
                false,
            );

            $conversation->addParticipant($participant1);
            $conversation->addParticipant($participant2);

            $this->conversationRepository->save($conversation);
        }

        return ConversationResponse::fromEntity($conversation);
    }
}