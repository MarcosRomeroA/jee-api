<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchTeams;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\TeamResponse;

final readonly class SearchTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamsSearcher $searcher,
    ) {
    }

    public function __invoke(SearchTeamsQuery $query): TeamCollectionResponse
    {
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;

        $teams = $this->searcher->search(
            $query->query,
            $gameId,
            $query->limit,
            $query->offset
        );

        $total = $this->searcher->count($query->query, $gameId);

        $teamsResponse = !empty($teams)
            ? array_map(static fn($team) => TeamResponse::fromTeam($team), $teams)
            : [];

        return new TeamCollectionResponse($teamsResponse, $total, $query->limit, $query->offset);
    }
}
