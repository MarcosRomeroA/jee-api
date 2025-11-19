<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentCollectionResponse;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;

final readonly class SearchTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private TournamentsSearcher $searcher,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(SearchTournamentsQuery $query): TournamentCollectionResponse
    {
        $gameId = $query->gameId ? new Uuid($query->gameId) : null;
        $statusId = $query->statusId ? new Uuid($query->statusId) : null;
        $responsibleId = $query->responsibleId ? new Uuid($query->responsibleId) : null;

        $tournaments = $this->searcher->search(
            $query->name,
            $gameId,
            $statusId,
            $responsibleId,
            $query->open,
            $query->limit,
            $query->offset
        );

        $total = $this->searcher->count($query->name, $gameId, $statusId, $responsibleId, $query->open);

        $tournamentsResponse = !empty($tournaments)
            ? array_map(fn($tournament) => TournamentResponse::fromTournament($tournament, $this->fileManager), $tournaments)
            : [];

        return new TournamentCollectionResponse($tournamentsResponse, $total, $query->limit, $query->offset);
    }
}
