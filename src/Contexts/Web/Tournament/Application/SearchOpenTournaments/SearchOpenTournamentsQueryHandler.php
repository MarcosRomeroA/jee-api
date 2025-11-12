<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchOpenTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentCollectionResponse;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;

final class SearchOpenTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly OpenTournamentsSearcher $searcher
    ) {
    }

    public function __invoke(SearchOpenTournamentsQuery $query): TournamentCollectionResponse
    {
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;

        $tournaments = $this->searcher->search($query->query, $gameId);

        $tournamentsResponse = array_map(
            static fn($tournament) => TournamentResponse::fromTournament($tournament),
            $tournaments
        );

        return new TournamentCollectionResponse($tournamentsResponse);
    }
}

