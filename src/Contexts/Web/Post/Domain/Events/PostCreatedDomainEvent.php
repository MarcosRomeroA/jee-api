<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class PostCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $id
    )
    {
        parent::__construct($id);
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
    ): DomainEvent
    {
        return new self($aggregateId);
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId(),
        ];
    }
}