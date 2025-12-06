<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\CommentRepository;
use App\Contexts\Web\Post\Domain\Events\CommentModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\Moderation\TextModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class CommentTextModerator
{
    public function __construct(
        private CommentRepository $commentRepository,
        private TextModerationService $textModerationService,
        private EntityManagerInterface $entityManager,
        private EventBus $eventBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Uuid $commentId): void
    {
        try {
            $comment = $this->commentRepository->findById($commentId);
        } catch (\Exception $e) {
            $this->logger->warning('Comment not found for text moderation', [
                'comment_id' => $commentId->value(),
            ]);
            return;
        }

        if ($comment->isDisabled()) {
            return;
        }

        $text = $comment->getComment()->value();

        $moderationReason = $this->textModerationService->moderate($text);

        if ($moderationReason === null) {
            return;
        }

        $comment->disable($moderationReason);
        $this->entityManager->flush();

        $this->eventBus->publish([new CommentModeratedDomainEvent($commentId, $moderationReason->value)]);

        $this->logger->info('Comment disabled due to text moderation', [
            'comment_id' => $commentId->value(),
            'reason' => $moderationReason->value,
        ]);
    }
}
