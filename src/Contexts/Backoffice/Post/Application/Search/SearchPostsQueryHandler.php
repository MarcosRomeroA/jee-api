<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Post\Application\Search;

use App\Contexts\Backoffice\Post\Application\Shared\PostCollectionResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchPostsQueryHandler implements QueryHandler
{
    public function __construct(
        private PostSearcher $searcher
    ) {
    }

    public function __invoke(SearchPostsQuery $query): PostCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}
