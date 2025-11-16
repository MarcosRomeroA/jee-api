<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostComments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\PostCommentCollectionResponse;

final readonly class SearchPostCommentsQueryHandler implements QueryHandler
{
    public function __construct(
        private PostCommentSearcher $searcher
    ) {
    }

    public function __invoke(SearchPostCommentsQuery $query): PostCommentCollectionResponse
    {
        return $this->searcher->__invoke($query->id, $query->limit ?? 10, $query->offset ?? 0);
    }
}
