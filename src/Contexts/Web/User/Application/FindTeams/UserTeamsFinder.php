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
            $team = $teamPlayer->getTeam();

            $games = $this->buildGamesArray($team);

            $teams[] = [
                "id" => $team->getId()->value(),
                "name" => $team->getName(),
                "image" => $team->getImage(),
                "games" => $games,
            ];
        }

        return $teams;
    }

    private function buildGamesArray($team): array
    {
        $games = [];

        foreach ($team->getTeamGames() as $teamGame) {
            $games[] = [
                "id" => $teamGame->getGame()->getId()->value(),
                "name" => $teamGame->getGame()->getName(),
            ];
        }

        return $games;
    }
}
