<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class PostCommentedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $id,
        array $body
    )
    {
        parent::__construct($id, $body);
    }

    public static function eventName(): string
    {
        return 'post.commented';
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): DomainEvent
    {
        return new self($aggregateId, $body);
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId(),
        ];
    }
}