<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Conversation\Application;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Conversation\Application\CreateMessage\MessageCreator;
use App\Contexts\Web\Conversation\Domain\Conversation;
use App\Contexts\Web\Conversation\Domain\ConversationRepository;
use App\Contexts\Web\Conversation\Domain\Message;
use App\Contexts\Web\Conversation\Domain\MessageRepository;
use App\Contexts\Web\Conversation\Domain\ValueObject\ContentValue;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Tests\Unit\Web\Conversation\Domain\ConversationMother;
use App\Tests\Unit\Web\Conversation\Domain\UserMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MessageCreatorTest extends TestCase
{
    private MessageRepository|MockObject $messageRepository;
    private ConversationRepository|MockObject $conversationRepository;
    private UserRepository|MockObject $userRepository;
    private EventBus|MockObject $bus;
    private MessageCreator $creator;

    protected function setUp(): void
    {
        $this->messageRepository = $this->createMock(MessageRepository::class);
        $this->conversationRepository = $this->createMock(ConversationRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->bus = $this->createMock(EventBus::class);

        $this->creator = new MessageCreator(
            $this->messageRepository,
            $this->conversationRepository,
            $this->userRepository,
            $this->bus
        );
    }

    public function testItShouldCreateMessage(): void
    {
        // Arrange
        $messageId = Uuid::random();
        $conversationId = Uuid::random();
        $userId = Uuid::random();
        $content = new ContentValue('Hello, this is a test message');

        $conversation = ConversationMother::create(id: $conversationId);
        $user = UserMother::create(id: $userId);

        $this->conversationRepository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->with($conversationId)
            ->willReturn($conversation);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->messageRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Message::class));

        $this->conversationRepository
            ->expects($this->once())
            ->method('save')
            ->with($conversation);

        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->isType('array'));

        // Act
        ($this->creator)($messageId, $conversationId, $userId, $content);

        // Assert - Expectations verified by mocks
        $this->assertTrue(true);
    }

    public function testItShouldUpdateConversationLastMessage(): void
    {
        // Arrange
        $messageId = Uuid::random();
        $conversationId = Uuid::random();
        $userId = Uuid::random();
        $content = new ContentValue('Latest message');

        $conversation = ConversationMother::create(id: $conversationId);
        $user = UserMother::create(id: $userId);

        $this->conversationRepository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->with($conversationId)
            ->willReturn($conversation);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        $this->messageRepository
            ->expects($this->once())
            ->method('save');

        // Verify conversation is saved (which includes updated last message)
        $this->conversationRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Conversation $conv) {
                return $conv->getLastMessage() instanceof Message;
            }));

        $this->bus
            ->expects($this->once())
            ->method('publish');

        // Act
        ($this->creator)($messageId, $conversationId, $userId, $content);

        // Assert
        $this->assertInstanceOf(Message::class, $conversation->getLastMessage());
    }

    public function testItShouldPublishDomainEvents(): void
    {
        // Arrange
        $messageId = Uuid::random();
        $conversationId = Uuid::random();
        $userId = Uuid::random();
        $content = new ContentValue('Message with events');

        $conversation = ConversationMother::create(id: $conversationId);
        $user = UserMother::create(id: $userId);

        $this->conversationRepository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->willReturn($conversation);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $this->messageRepository
            ->expects($this->once())
            ->method('save');

        $this->conversationRepository
            ->expects($this->once())
            ->method('save');

        // Verify events are published
        $this->bus
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(function (array $events) {
                return is_array($events) && !empty($events);
            }));

        // Act
        ($this->creator)($messageId, $conversationId, $userId, $content);

        // Assert
        $this->assertTrue(true);
    }

    public function testItShouldCreateMessageWithNormalContent(): void
    {
        // Arrange
        $messageId = Uuid::random();
        $conversationId = Uuid::random();
        $userId = Uuid::random();
        $normalText = 'This is a normal message with reasonable length.';
        $content = new ContentValue($normalText);

        $conversation = ConversationMother::create(id: $conversationId);
        $user = UserMother::create(id: $userId);

        $this->conversationRepository
            ->expects($this->once())
            ->method('findByIdOrFail')
            ->willReturn($conversation);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $this->messageRepository
            ->expects($this->once())
            ->method('save');

        $this->conversationRepository
            ->expects($this->once())
            ->method('save');

        $this->bus
            ->expects($this->once())
            ->method('publish');

        // Act
        ($this->creator)($messageId, $conversationId, $userId, $content);

        // Assert
        $this->assertTrue(true);
    }
}
