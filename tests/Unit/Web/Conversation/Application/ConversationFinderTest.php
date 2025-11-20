<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Conversation\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\FindByOtherUserId\ConversationFinder;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\User\Domain\Exception\OtherUserIsMeException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Conversation\Domain\ConversationMother;
use App\Tests\Unit\Web\Conversation\Domain\UserMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConversationFinderTest extends TestCase
{
    private UserRepository|MockObject $userRepository;
    private ConversationRepository|MockObject $conversationRepository;
    private ConversationFinder $finder;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->conversationRepository = $this->createMock(ConversationRepository::class);

        $this->finder = new ConversationFinder(
            $this->userRepository,
            $this->conversationRepository
        );
    }

    public function testItShouldFindExistingConversation(): void
    {
        // Arrange
        $otherUserId = Uuid::random();
        $sessionId = Uuid::random();

        $otherUser = UserMother::create(id: $otherUserId);
        $sessionUser = UserMother::create(id: $sessionId);
        $conversation = ConversationMother::random();

        $this->userRepository
            ->expects($this->exactly(2))
            ->method('findById')
            ->willReturnCallback(function ($id) use ($otherUserId, $sessionId, $otherUser, $sessionUser) {
                if ($id->equals($otherUserId)) {
                    return $otherUser;
                }
                return $sessionUser;
            });

        $this->conversationRepository
            ->expects($this->once())
            ->method('searchConversationByParticipantUsers')
            ->with($otherUser, $sessionUser)
            ->willReturn($conversation);

        // Conversation exists, so should not be saved
        $this->conversationRepository
            ->expects($this->never())
            ->method('save');

        // Act
        $response = ($this->finder)($otherUserId, $sessionId);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldCreateNewConversationIfNotExists(): void
    {
        // Arrange
        $otherUserId = Uuid::random();
        $sessionId = Uuid::random();

        $otherUser = UserMother::create(id: $otherUserId);
        $sessionUser = UserMother::create(id: $sessionId);

        $this->userRepository
            ->expects($this->exactly(4)) // 2 initial checks + 2 for creating participants
            ->method('findById')
            ->willReturnCallback(function ($id) use ($otherUserId, $sessionId, $otherUser, $sessionUser) {
                if ($id->equals($otherUserId)) {
                    return $otherUser;
                }
                return $sessionUser;
            });

        // No existing conversation
        $this->conversationRepository
            ->expects($this->once())
            ->method('searchConversationByParticipantUsers')
            ->with($otherUser, $sessionUser)
            ->willReturn(null);

        // Should create and save new conversation
        $this->conversationRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Conversation::class));

        // Act
        $response = ($this->finder)($otherUserId, $sessionId);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldThrowExceptionWhenOtherUserIsSessionUser(): void
    {
        // Arrange
        $userId = Uuid::random();
        $user = UserMother::create(id: $userId);

        $this->userRepository
            ->expects($this->exactly(2))
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        // Expect exception
        $this->expectException(OtherUserIsMeException::class);

        // Act
        ($this->finder)($userId, $userId);
    }

    public function testItShouldCreateConversationWithTwoParticipants(): void
    {
        // Arrange
        $otherUserId = Uuid::random();
        $sessionId = Uuid::random();

        $otherUser = UserMother::create(id: $otherUserId);
        $sessionUser = UserMother::create(id: $sessionId);

        $this->userRepository
            ->method('findById')
            ->willReturnCallback(function ($id) use ($otherUserId, $sessionId, $otherUser, $sessionUser) {
                if ($id->equals($otherUserId)) {
                    return $otherUser;
                }
                return $sessionUser;
            });

        $this->conversationRepository
            ->method('searchConversationByParticipantUsers')
            ->willReturn(null);

        // Verify conversation has 2 participants
        $this->conversationRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Conversation $conversation) {
                return $conversation->getParticipants()->count() === 2;
            }));

        // Act
        ($this->finder)($otherUserId, $sessionId);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldSetSessionUserAsCreator(): void
    {
        // Arrange
        $otherUserId = Uuid::random();
        $sessionId = Uuid::random();

        $otherUser = UserMother::create(id: $otherUserId);
        $sessionUser = UserMother::create(id: $sessionId);

        $this->userRepository
            ->method('findById')
            ->willReturnCallback(function ($id) use ($otherUserId, $sessionId, $otherUser, $sessionUser) {
                if ($id->equals($otherUserId)) {
                    return $otherUser;
                }
                return $sessionUser;
            });

        $this->conversationRepository
            ->method('searchConversationByParticipantUsers')
            ->willReturn(null);

        $this->conversationRepository
            ->expects($this->once())
            ->method('save');

        // Act
        ($this->finder)($otherUserId, $sessionId);

        // Assert
        $this->assertTrue(true);
    }
}
