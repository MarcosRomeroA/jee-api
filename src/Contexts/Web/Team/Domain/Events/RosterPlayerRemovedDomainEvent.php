<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

class RosterPlayerRemovedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $id,
        private readonly string $rosterId,
        private readonly string $playerId
    ) {
        parent::__construct($id);
    }

    public static function eventName(): string
    {
        return 'roster_player.removed';
    }

    public function rosterId(): string
    {
        return $this->rosterId;
    }

    public function playerId(): string
    {
        return $this->playerId;
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): DomainEvent {
        return new self(
            $aggregateId,
            $body['rosterId'] ?? '',
            $body['playerId'] ?? ''
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->getAggregateId(),
            'rosterId' => $this->rosterId,
            'playerId' => $this->playerId,
        ];
    }
}
