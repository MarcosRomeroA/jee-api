<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;

final readonly class ImageModerationOnPostCreatedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostImageModerator $postImageModerator,
    ) {
    }

    public function __invoke(PostCreatedDomainEvent $event): void
    {
        $this->postImageModerator->__invoke($event->getAggregateId());
    }

    public static function subscribedTo(): array
    {
        return [PostCreatedDomainEvent::class];
    }
}
