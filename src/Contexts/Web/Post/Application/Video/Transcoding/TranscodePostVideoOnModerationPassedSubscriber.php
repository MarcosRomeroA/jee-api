<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Video\Transcoding;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Domain\Events\PostVideoModerationPassedDomainEvent;

final readonly class TranscodePostVideoOnModerationPassedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PostVideoTranscoder $transcoder,
    ) {
    }

    public function __invoke(PostVideoModerationPassedDomainEvent $event): void
    {
        $data = $event->toPrimitives();
        $postId = $data['postId'] instanceof Uuid ? $data['postId'] : new Uuid($data['postId']);

        $this->transcoder->__invoke(
            $postId,
            $data['resourceId'],
            $data['originalVideoFilename']
        );
    }

    public static function subscribedTo(): array
    {
        return [PostVideoModerationPassedDomainEvent::class];
    }
}
