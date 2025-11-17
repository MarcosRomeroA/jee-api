<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTeams;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamPlayerRepository;

final readonly class UserTeamsFinder
{
    public function __construct(
        private TeamPlayerRepository $teamPlayerRepository
    ) {
    }

    public function __invoke(Uuid $userId): array
    {
        $teamPlayers = $this->teamPlayerRepository->findByPlayerId($userId);

        $teams = [];
        foreach ($teamPlayers as $teamPlayer) {
            $team = $teamPlayer->team();

            $games = $this->buildGamesArray($team);

            $teams[] = [
                "id" => $team->id()->value(),
                "name" => $team->name(),
                "image" => $team->image(),
                "games" => $games,
            ];
        }

        return $teams;
    }

    private function buildGamesArray($team): array
    {
        $games = [];

        foreach ($team->teamGames() as $teamGame) {
            $games[] = [
                "id" => $teamGame->game()->getId()->value(),
                "name" => $teamGame->game()->getName(),
            ];
        }

        return $games;
    }
}
