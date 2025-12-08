<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class PostCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $id,
        array $resources
    ) {
        $body['resources'] = $resources;
        parent::__construct($id, $body);
    }

    public static function eventName(): string
    {
        return 'post.created';
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): DomainEvent {
        return new self($aggregateId, $body['resources']);
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId()->value(),
            'resources' => $this->body['resources'],
        ];
    }
}
