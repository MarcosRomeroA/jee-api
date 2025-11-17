<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemoveGame;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\Exception\GameNotFoundException;
use App\Contexts\Web\Game\Domain\Game;
use App\Contexts\Web\Game\Domain\GameRepository;
use App\Contexts\Web\Team\Domain\Exception\TeamGameNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamGame;
use App\Contexts\Web\Team\Domain\TeamGameRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamGameRemover
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
        $teamGame = $this->findTeamGame($team, $game);

        $this->teamGameRepository->remove($teamGame);
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

    private function findTeamGame(Team $team, Game $game): TeamGame
    {
        $teamGame = $this->teamGameRepository->findByTeamAndGame($team, $game);

        if ($teamGame === null) {
            throw new TeamGameNotFoundException(
                sprintf('Team game not found for team %s and game %s', $team->getId()->value(), $game->getId()->value())
            );
        }

        return $teamGame;
    }
}
