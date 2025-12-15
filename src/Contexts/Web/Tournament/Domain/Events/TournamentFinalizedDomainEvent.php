<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TournamentFinalizedDomainEvent extends DomainEvent
{
    public function __construct(
        string $tournamentId,
        private readonly string $firstPlaceTeamId,
        private readonly ?string $secondPlaceTeamId,
        private readonly ?string $thirdPlaceTeamId,
    ) {
        parent::__construct(new Uuid($tournamentId));
    }

    public static function eventName(): string
    {
        return "tournament.finalized";
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId->value(),
            $body["firstPlaceTeamId"],
            $body["secondPlaceTeamId"] ?? null,
            $body["thirdPlaceTeamId"] ?? null,
        );
    }

    public function toPrimitives(): array
    {
        return [
            "tournamentId" => $this->getAggregateId()->value(),
            "firstPlaceTeamId" => $this->firstPlaceTeamId,
            "secondPlaceTeamId" => $this->secondPlaceTeamId,
            "thirdPlaceTeamId" => $this->thirdPlaceTeamId,
        ];
    }

    public function firstPlaceTeamId(): string
    {
        return $this->firstPlaceTeamId;
    }

    public function secondPlaceTeamId(): ?string
    {
        return $this->secondPlaceTeamId;
    }

    public function thirdPlaceTeamId(): ?string
    {
        return $this->thirdPlaceTeamId;
    }
}
