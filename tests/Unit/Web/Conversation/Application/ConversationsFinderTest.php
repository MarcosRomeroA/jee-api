<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Conversation\Application;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\FindConversations\ConversationsFinder;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Conversation\Domain\ConversationMother;
use App\Tests\Unit\Web\Conversation\Domain\UserMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConversationsFinderTest extends TestCase
{
    private const CDN_BASE_URL = 'https://cdn.example.com';

    private UserRepository|MockObject $userRepository;
    private ConversationRepository|MockObject $conversationRepository;
    private MessageRepository|MockObject $messageRepository;
    private ConversationsFinder $finder;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->conversationRepository = $this->createMock(ConversationRepository::class);
        $this->messageRepository = $this->createMock(MessageRepository::class);

        $this->finder = new ConversationsFinder(
            $this->userRepository,
            $this->conversationRepository,
            $this->messageRepository,
            self::CDN_BASE_URL,
        );
    }

    public function testItShouldFindConversationsForUser(): void
    {
        // Arrange
        $sessionId = Uuid::random();
        $sessionUser = UserMother::create(id: $sessionId);

        $conversations = [
            ConversationMother::random(),
            ConversationMother::random(),
            ConversationMother::random(),
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($sessionId)
            ->willReturn($sessionUser);

        $this->conversationRepository
            ->expects($this->once())
            ->method('searchConversations')
            ->with($sessionUser)
            ->willReturn($conversations);

        $this->messageRepository
            ->expects($this->exactly(3))
            ->method('countUnreadMessagesForUser')
            ->willReturn(0);

        // Act
        $response = ($this->finder)($sessionId);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldReturnEmptyListWhenNoConversations(): void
    {
        // Arrange
        $sessionId = Uuid::random();
        $sessionUser = UserMother::create(id: $sessionId);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($sessionId)
            ->willReturn($sessionUser);

        $this->conversationRepository
            ->expects($this->once())
            ->method('searchConversations')
            ->with($sessionUser)
            ->willReturn([]);

        $this->messageRepository
            ->expects($this->never())
            ->method('countUnreadMessagesForUser');

        // Act
        $response = ($this->finder)($sessionId);

        // Assert
        $this->assertNotNull($response);
    }

    public function testItShouldFindMultipleConversations(): void
    {
        // Arrange
        $sessionId = Uuid::random();
        $sessionUser = UserMother::create(id: $sessionId);

        $conversations = [
            ConversationMother::random(),
            ConversationMother::random(),
            ConversationMother::random(),
            ConversationMother::random(),
            ConversationMother::random(),
        ];

        $this->userRepository
            ->method('findById')
            ->willReturn($sessionUser);

        $this->conversationRepository
            ->method('searchConversations')
            ->willReturn($conversations);

        $this->messageRepository
            ->expects($this->exactly(5))
            ->method('countUnreadMessagesForUser')
            ->willReturn(0);

        // Act
        $response = ($this->finder)($sessionId);

        // Assert
        $this->assertNotNull($response);
    }
}
