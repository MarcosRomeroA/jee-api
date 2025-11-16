<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchPostLikes;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\LikeCollectionResponse;

final readonly class SearchPostLikesQueryHandler implements QueryHandler
{
    public function __construct(
        private PostLikesSearcher $searcher
    ) {
    }

    public function __invoke(SearchPostLikesQuery $query): LikeCollectionResponse
    {
        return $this->searcher->__invoke($query->postId, $query->limit ?? 10, $query->offset ?? 0);
    }
}
