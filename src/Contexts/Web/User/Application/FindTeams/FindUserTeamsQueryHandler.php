<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTeams;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamPlayerRepository;

final readonly class FindUserTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamPlayerRepository $teamPlayerRepository,
    ) {}

    public function __invoke(FindUserTeamsQuery $query): array
    {
        $userId = new Uuid($query->userId);

        // Obtener todos los TeamPlayer del usuario
        $teamPlayers = $this->teamPlayerRepository->findByPlayerId($userId);

        $teams = [];
        foreach ($teamPlayers as $teamPlayer) {
            $team = $teamPlayer->team();

            $games = [];
            foreach ($team->teamGames() as $teamGame) {
                $games[] = [
                    "id" => $teamGame->game()->getId()->value(),
                    "name" => $teamGame->game()->getName(),
                ];
            }

            $teams[] = [
                "id" => $team->id()->value(),
                "name" => $team->name(),
                "image" => $team->image(),
                "games" => $games,
            ];
        }

        return $teams;
    }
}
