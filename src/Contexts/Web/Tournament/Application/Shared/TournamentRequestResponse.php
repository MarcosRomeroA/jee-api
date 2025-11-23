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
            $request->id()->value(),
            $request->tournament()->id()->value(),
            $request->tournament()->name(),
            $request->team()->id()->value(),
            $request->team()->name(),
            $request->status(),
            $request->createdAt()->format(\DateTimeInterface::ATOM),
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
