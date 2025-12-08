<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class PostVideoModerationPassedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $postId,
        string $resourceId,
        string $originalVideoFilename,
    ) {
        $body = [
            'resourceId' => $resourceId,
            'originalVideoFilename' => $originalVideoFilename,
        ];
        parent::__construct($postId, $body);
    }

    public static function eventName(): string
    {
        return 'post.video.moderation_passed';
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): DomainEvent {
        return new self(
            $aggregateId,
            $body['resourceId'],
            $body['originalVideoFilename'],
        );
    }

    public function toPrimitives(): array
    {
        return [
            'postId' => $this->getAggregateId()->value(),
            'resourceId' => $this->body['resourceId'],
            'originalVideoFilename' => $this->body['originalVideoFilename'],
        ];
    }
}
