<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchStatus;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchTournamentStatusQueryHandler implements QueryHandler
{
    public function __construct(
        private TournamentStatusSearcher $searcher,
    ) {
    }

    public function __invoke(SearchTournamentStatusQuery $query): TournamentStatusCollectionResponse
    {
        $statuses = $this->searcher->search();

        $statusesResponse = array_map(
            static fn($status) => TournamentStatusResponse::fromEntity($status),
            $statuses,
        );

        return new TournamentStatusCollectionResponse($statusesResponse);
    }
}
