<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostShares;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\ShareCollectionResponse;

final readonly class SearchPostSharesQueryHandler implements QueryHandler
{
    public function __construct(
        private PostSharesSearcher $searcher
    ) {
    }

    public function __invoke(SearchPostSharesQuery $query): ShareCollectionResponse
    {
        return $this->searcher->__invoke($query->postId, $query->limit ?? 10, $query->offset ?? 0);
    }
}
