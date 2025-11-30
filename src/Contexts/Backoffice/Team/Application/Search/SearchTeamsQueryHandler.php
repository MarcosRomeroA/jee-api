<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Search;

use App\Contexts\Backoffice\Team\Application\Shared\TeamCollectionResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamSearcher $searcher,
    ) {
    }

    public function __invoke(SearchTeamsQuery $query): TeamCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}
