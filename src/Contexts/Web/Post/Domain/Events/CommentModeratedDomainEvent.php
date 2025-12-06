<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class CommentModeratedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $commentId,
        private readonly string $moderationReason,
    ) {
        parent::__construct($commentId);
    }

    public static function eventName(): string
    {
        return "comment.moderated";
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId,
            $body["moderationReason"],
        );
    }

    public function toPrimitives(): array
    {
        return [
            "commentId" => $this->getAggregateId()->value(),
            "moderationReason" => $this->moderationReason,
        ];
    }

    public function moderationReason(): string
    {
        return $this->moderationReason;
    }
}
