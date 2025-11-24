<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TeamRequestCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $requestId,
        private readonly Uuid $teamId,
        private readonly Uuid $userId,
    ) {
        parent::__construct($requestId);
    }

    public static function eventName(): string
    {
        return "team.request.created";
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId,
            new Uuid($body["teamId"]),
            new Uuid($body["userId"]),
        );
    }

    public function toPrimitives(): array
    {
        return [
            "requestId" => $this->getAggregateId()->value(),
            "teamId" => $this->teamId->value(),
            "userId" => $this->userId->value(),
        ];
    }

    public function teamId(): Uuid
    {
        return $this->teamId;
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }
}
