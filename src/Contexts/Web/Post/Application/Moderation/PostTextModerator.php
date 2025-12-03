<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\Moderation\TextModerationService;
use App\Contexts\Web\Post\Domain\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class PostTextModerator
{
    public function __construct(
        private PostRepository $postRepository,
        private TextModerationService $textModerationService,
        private EntityManagerInterface $entityManager,
        private EventBus $eventBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Uuid $postId): void
    {
        try {
            $post = $this->postRepository->findById($postId);
        } catch (\Exception $e) {
            $this->logger->warning('Post not found for text moderation', [
                'post_id' => $postId->value(),
            ]);
            return;
        }

        if ($post->isDisabled()) {
            return;
        }

        $text = $post->getBody()->value();

        $moderationReason = $this->textModerationService->moderate($text);

        if ($moderationReason === null) {
            return;
        }

        $post->disable($moderationReason);
        $this->entityManager->flush();

        $this->eventBus->publish([new PostModeratedDomainEvent($postId, $moderationReason->value)]);

        $this->logger->info('Post disabled due to text moderation', [
            'post_id' => $postId->value(),
            'reason' => $moderationReason->value,
        ]);
    }
}
