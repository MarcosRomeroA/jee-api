<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchTeams;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\TeamResponse;

final class SearchTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly TeamsSearcher $searcher
    ) {
    }

    public function __invoke(SearchTeamsQuery $query): TeamCollectionResponse
    {
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;

        $teams = $this->searcher->search($query->query, $gameId);

        $teamsResponse = array_map(
            static fn($team) => TeamResponse::fromTeam($team),
            $teams
        );

        return new TeamCollectionResponse($teamsResponse);
    }
}

