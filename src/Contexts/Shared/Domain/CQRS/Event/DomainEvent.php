<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Event;

use Jee\Contexts\Shared\Domain\Utils;
use Jee\Contexts\Shared\Domain\ValueObject\Uuid;
use DateTimeImmutable;

abstract class DomainEvent
{
    private string $eventId;
    private string $occurredOn;

    public function __construct(private Uuid|int|string|null$aggregateId, ?string $eventId = null, ?string $occurredOn = null)
    {
        $this->eventId    = $eventId ?: Uuid::random()->value();
        $this->occurredOn = $occurredOn ?: Utils::dateToString(new DateTimeImmutable());
    }

    abstract public static function fromPrimitives(
        string|int $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function aggregateId(): Uuid|string|int
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
    public function setAggregateId(int $value): void
    {
        $this->aggregateId= $value;
    }
}