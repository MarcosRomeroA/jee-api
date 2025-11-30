<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Search;

use App\Contexts\Backoffice\Tournament\Application\Shared\TournamentCollectionResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private TournamentSearcher $searcher,
    ) {
    }

    public function __invoke(SearchTournamentsQuery $query): TournamentCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}
