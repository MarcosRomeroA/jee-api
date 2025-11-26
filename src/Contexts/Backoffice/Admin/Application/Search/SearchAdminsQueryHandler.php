<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Admin\Application\Search;

use App\Contexts\Backoffice\Admin\Application\Shared\AdminCollectionResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchAdminsQueryHandler implements QueryHandler
{
    public function __construct(
        private AdminSearcher $searcher
    ) {
    }

    public function __invoke(SearchAdminsQuery $query): AdminCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}
