<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Tournament\Domain\TournamentRequest;

final class TournamentRequestResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $tournamentId,
        public readonly string $tournamentName,
        public readonly string $teamId,
        public readonly string $teamName,
        public readonly string $status,
        public readonly string $createdAt,
    ) {
    }

    public static function fromTournamentRequest(TournamentRequest $request): self
    {
        return new self(
            $request->getId()->value(),
            $request->getTournament()->getId()->value(),
            $request->getTournament()->getName(),
            $request->getTeam()->getId()->value(),
            $request->getTeam()->getName(),
            $request->getStatus(),
            $request->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tournamentId' => $this->tournamentId,
            'tournamentName' => $this->tournamentName,
            'teamId' => $this->teamId,
            'teamName' => $this->teamName,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
        ];
    }
}
