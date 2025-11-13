<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchMyTeams;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\TeamResponse;

final readonly class SearchMyTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private MyTeamsSearcher $searcher,
    ) {
    }

    public function __invoke(SearchMyTeamsQuery $query): TeamCollectionResponse
    {
        $userId = new Uuid($query->ownerId);
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;

        $teams = $this->searcher->search(
            $userId,
            $query->query,
            $gameId,
            $query->limit,
            $query->offset
        );

        $total = $this->searcher->count($userId, $query->query, $gameId);

        $teamsResponse = !empty($teams)
            ? array_map(static fn($team) => TeamResponse::fromTeam($team), $teams)
            : [];


        return new TeamCollectionResponse($teamsResponse, $total, $query->limit, $query->offset);
    }
}
