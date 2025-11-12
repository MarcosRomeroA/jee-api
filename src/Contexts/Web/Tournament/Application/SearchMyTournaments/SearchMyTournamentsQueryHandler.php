<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchMyTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentCollectionResponse;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;

final class SearchMyTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly MyTournamentsSearcher $searcher
    ) {
    }

    public function __invoke(SearchMyTournamentsQuery $query): TournamentCollectionResponse
    {
        $responsibleId = new Uuid($query->responsibleId);
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;

        $tournaments = $this->searcher->search($responsibleId, $query->query, $gameId);

        $tournamentsResponse = array_map(
            static fn($tournament) => TournamentResponse::fromTournament($tournament),
            $tournaments
        );

        return new TournamentCollectionResponse($tournamentsResponse);
    }
}

