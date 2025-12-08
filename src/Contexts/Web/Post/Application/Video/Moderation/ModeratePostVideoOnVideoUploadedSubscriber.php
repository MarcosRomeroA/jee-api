<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Video\Moderation;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostVideoUploadedDomainEvent;

final readonly class ModeratePostVideoOnVideoUploadedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostVideoModerator $moderator,
    ) {
    }

    public function __invoke(PostVideoUploadedDomainEvent $event): void
    {
        $data = $event->toPrimitives();
        $postId = $data['postId'] instanceof Uuid ? $data['postId'] : new Uuid($data['postId']);

        $this->moderator->__invoke(
            $postId,
            $data['resourceId'],
            $data['frameFilenames']
        );
    }

    public static function subscribedTo(): array
    {
        return [PostVideoUploadedDomainEvent::class];
    }
}
