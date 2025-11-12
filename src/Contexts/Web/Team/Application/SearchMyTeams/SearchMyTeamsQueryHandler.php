<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchMyTeams;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\TeamResponse;

final class SearchMyTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly MyTeamsSearcher $searcher
    ) {
    }

    public function __invoke(SearchMyTeamsQuery $query): TeamCollectionResponse
    {
        $teams = $this->searcher->search(
            new Uuid($query->ownerId),
            $query->query
        );

        $teamsResponse = array_map(
            static fn($team) => TeamResponse::fromTeam($team),
            $teams
        );

        return new TeamCollectionResponse($teamsResponse);
    }
}

