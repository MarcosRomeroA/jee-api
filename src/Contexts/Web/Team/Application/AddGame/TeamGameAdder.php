<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddGame;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamGameRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamGameAdder
{
    public function __construct(
        private TeamRepository $teamRepository,
        private GameRepository $gameRepository,
        private TeamGameRepository $teamGameRepository
    ) {
    }

    public function __invoke(Uuid $teamId, Uuid $gameId): void
    {
        $team = $this->findTeam($teamId);
        $game = $this->findGame($gameId);

        if ($this->gameAlreadyExists($teamId, $gameId)) {
            return; // Game already exists, idempotent operation
        }

        $team->addGame($game);
        $this->teamRepository->save($team);
    }

    private function findTeam(Uuid $teamId): Team
    {
        $team = $this->teamRepository->findById($teamId);

        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }

        return $team;
    }

    private function findGame(Uuid $gameId): Game
    {
        $game = $this->gameRepository->findById($gameId);

        if ($game === null) {
            throw new GameNotFoundException($gameId->value());
        }

        return $game;
    }

    private function gameAlreadyExists(Uuid $teamId, Uuid $gameId): bool
    {
        return $this->teamGameRepository->existsByTeamAndGame($teamId, $gameId);
    }
}
