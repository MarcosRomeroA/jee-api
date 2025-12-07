<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\Events;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEvent;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TournamentRequestAcceptedDomainEvent extends DomainEvent
{
    public function __construct(
        Uuid $requestId,
        private readonly Uuid $tournamentId,
        private readonly Uuid $teamId,
    ) {
        parent::__construct($requestId);
    }

    public static function eventName(): string
    {
        return 'tournament.request.accepted';
    }

    public static function fromPrimitives(
        Uuid $aggregateId,
        ?array $body,
        ?string $eventId,
        ?string $occurredOn,
    ): DomainEvent {
        return new self(
            $aggregateId,
            new Uuid($body['tournamentId']),
            new Uuid($body['teamId']),
        );
    }

    public function toPrimitives(): array
    {
        return [
            'requestId' => $this->getAggregateId()->value(),
            'tournamentId' => $this->tournamentId->value(),
            'teamId' => $this->teamId->value(),
        ];
    }

    public function tournamentId(): Uuid
    {
        return $this->tournamentId;
    }

    public function teamId(): Uuid
    {
        return $this->teamId;
    }
}
