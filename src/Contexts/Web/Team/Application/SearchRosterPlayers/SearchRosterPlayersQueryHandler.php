<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosterPlayers;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterPlayersCollectionResponse;

final readonly class SearchRosterPlayersQueryHandler implements QueryHandler
{
    public function __construct(private RosterPlayersSearcher $searcher)
    {
    }

    public function __invoke(SearchRosterPlayersQuery $query): RosterPlayersCollectionResponse
    {
        return $this->searcher->__invoke(
            new Uuid($query->rosterId),
            new Uuid($query->teamId),
        );
    }
}
