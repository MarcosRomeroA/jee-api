<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class PostVideoUploadedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $postId,
        string $resourceId,
        array $frameFilenames,
    ) {
        $body = [
            'resourceId' => $resourceId,
            'frameFilenames' => $frameFilenames,
        ];
        parent::__construct($postId, $body);
    }

    public static function eventName(): string
    {
        return 'post.video.uploaded';
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
            $body['frameFilenames'],
        );
    }

    public function toPrimitives(): array
    {
        return [
            'postId' => $this->getAggregateId()->value(),
            'resourceId' => $this->body['resourceId'],
            'frameFilenames' => $this->body['frameFilenames'],
        ];
    }
}
