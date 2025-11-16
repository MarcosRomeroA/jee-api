<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindTeamGames;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamGameCollectionResponse;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamGameRepository;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class FindTeamGamesQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamGameRepository $teamGameRepository
    ) {
    }

    public function __invoke(FindTeamGamesQuery $query): TeamGameCollectionResponse
    {
        $teamId = new Uuid($query->teamId);

        // Verify team exists
        $team = $this->teamRepository->findById($teamId);

        // Find all games for the team
        $teamGames = $this->teamGameRepository->findByTeam($teamId);

        return TeamGameCollectionResponse::fromTeamGames($teamGames);
    }
}
