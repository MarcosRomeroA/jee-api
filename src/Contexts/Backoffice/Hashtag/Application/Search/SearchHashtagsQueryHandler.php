<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Hashtag\Application\Search;

use App\Contexts\Backoffice\Hashtag\Application\Shared\HashtagCollectionResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchHashtagsQueryHandler implements QueryHandler
{
    public function __construct(
        private HashtagSearcher $searcher
    ) {
    }

    public function __invoke(SearchHashtagsQuery $query): HashtagCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}
