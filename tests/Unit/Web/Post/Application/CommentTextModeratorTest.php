<?php

declare(strict_types=1);

namespace App\Tests\Unit\Web\Post\Application;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Moderation\CommentTextModerator;
use App\Contexts\Web\Post\Domain\CommentRepository;
use App\Contexts\Web\Post\Domain\Events\CommentModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\Moderation\TextModerationService;
use App\Tests\Unit\Web\Post\Domain\CommentMother;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CommentTextModeratorTest extends TestCase
{
    private CommentRepository|MockObject $commentRepository;
    private TextModerationService|MockObject $textModerationService;
    private EntityManagerInterface|MockObject $entityManager;
    private EventBus|MockObject $eventBus;
    private LoggerInterface|MockObject $logger;
    private CommentTextModerator $moderator;

    protected function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->textModerationService = $this->createMock(TextModerationService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->moderator = new CommentTextModerator(
            $this->commentRepository,
            $this->textModerationService,
            $this->entityManager,
            $this->eventBus,
            $this->logger,
        );
    }

    public function testItShouldDisableCommentWhenModerationFails(): void
    {
        $commentId = Uuid::random();
        $comment = CommentMother::create(id: $commentId);

        $this->commentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($commentId)
            ->willReturn($comment);

        $this->textModerationService
            ->expects($this->once())
            ->method('moderate')
            ->with($comment->getComment()->value())
            ->willReturn(ModerationReason::HARASSMENT);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->eventBus
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(function (array $events) use ($commentId) {
                return count($events) === 1
                    && $events[0] instanceof CommentModeratedDomainEvent
                    && $events[0]->getAggregateId()->value() === $commentId->value()
                    && $events[0]->moderationReason() === ModerationReason::HARASSMENT->value;
            }));

        $this->logger
            ->expects($this->once())
            ->method('info');

        ($this->moderator)($commentId);

        $this->assertTrue($comment->isDisabled());
        $this->assertEquals(ModerationReason::HARASSMENT, $comment->getModerationReason());
    }

    public function testItShouldNotDisableCommentWhenModerationPasses(): void
    {
        $commentId = Uuid::random();
        $comment = CommentMother::create(id: $commentId);

        $this->commentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($commentId)
            ->willReturn($comment);

        $this->textModerationService
            ->expects($this->once())
            ->method('moderate')
            ->with($comment->getComment()->value())
            ->willReturn(null);

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->eventBus
            ->expects($this->never())
            ->method('publish');

        ($this->moderator)($commentId);

        $this->assertFalse($comment->isDisabled());
    }

    public function testItShouldNotModerateAlreadyDisabledComment(): void
    {
        $commentId = Uuid::random();
        $comment = CommentMother::create(id: $commentId);
        $comment->disable(ModerationReason::HATE_SPEECH);

        $this->commentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($commentId)
            ->willReturn($comment);

        $this->textModerationService
            ->expects($this->never())
            ->method('moderate');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->eventBus
            ->expects($this->never())
            ->method('publish');

        ($this->moderator)($commentId);
    }

    public function testItShouldLogWarningWhenCommentNotFound(): void
    {
        $commentId = Uuid::random();

        $this->commentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($commentId)
            ->willThrowException(new \Exception('Comment not found'));

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with('Comment not found for text moderation', [
                'comment_id' => $commentId->value(),
            ]);

        $this->textModerationService
            ->expects($this->never())
            ->method('moderate');

        ($this->moderator)($commentId);
    }
}
