<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Post\Domain\Events\PostCommentedDomainEvent;

final readonly class TextModerationOnCommentCreatedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private CommentTextModerator $commentTextModerator,
    ) {
    }

    public function __invoke(PostCommentedDomainEvent $event): void
    {
        $this->commentTextModerator->__invoke($event->commentId());
    }

    public static function subscribedTo(): array
    {
        return [PostCommentedDomainEvent::class];
    }
}
