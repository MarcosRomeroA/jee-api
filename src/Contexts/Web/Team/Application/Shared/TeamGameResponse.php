<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\Team\Domain\TeamGame;

final class TeamGameResponse extends Response
{
    public function __construct(
        public readonly string $id,
        public readonly string $teamId,
        public readonly string $gameId,
        public readonly string $gameName,
        public readonly string $addedAt
    ) {
    }

    public static function fromTeamGame(TeamGame $teamGame): self
    {
        return new self(
            $teamGame->getId()->value(),
            $teamGame->getTeam()->getId()->value(),
            $teamGame->getGame()->getId()->value(),
            $teamGame->getGame()->getName(),
            $teamGame->getAddedAt()->format(\DateTimeInterface::ATOM)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'teamId' => $this->teamId,
            'gameId' => $this->gameId,
            'gameName' => $this->gameName,
            'addedAt' => $this->addedAt
        ];
    }
}
