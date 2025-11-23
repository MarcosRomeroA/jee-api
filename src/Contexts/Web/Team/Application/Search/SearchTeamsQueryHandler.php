<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Search;

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
        $creatorId = $query->creatorId ? new Uuid($query->creatorId) : null;
        $userId = $query->userId ? new Uuid($query->userId) : null;
        $tournamentId = $query->tournamentId ? new Uuid($query->tournamentId) : null;

        $teams = $this->searcher->search(
            $query->query,
            $gameId,
            $creatorId,
            $userId,
            $tournamentId,
            $query->limit,
            $query->offset
        );

        $total = $this->searcher->count($query->query, $gameId, $creatorId, $userId, $tournamentId);

        $teamsResponse = !empty($teams)
            ? array_map(static fn ($team) => TeamResponse::fromTeam($team), $teams)
            : [];

        return new TeamCollectionResponse($teamsResponse, $total, $query->limit, $query->offset);
    }
}
