<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\CQRS\Event;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Domain\Utils;
use DateTimeImmutable;

abstract class DomainEvent
{
    private string $eventId;
    private string $occurredOn;
    protected array $body;

    public function __construct(private Uuid $aggregateId, ?array $body = [], ?string $eventId = null, ?string $occurredOn = null)
    {
        $this->eventId = $eventId ?: Uuid::random()->value();
        $this->occurredOn = $occurredOn ?: Utils::dateToString(new DateTimeImmutable());
        $this->body = $body;
    }

    abstract public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function getAggregateId(): Uuid
    {
        return $this->aggregateId;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getOccurredOn(): string
    {
        return $this->occurredOn;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setAggregateId(Uuid $value): void
    {
        $this->aggregateId = $value;
    }
}