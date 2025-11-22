<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use Exception;

final readonly class SearchPostQueryHandler implements QueryHandler
{
    public function __construct(
        private PostSearcher $searcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SearchPostQuery $query): PostCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria, $query->currentUserId);
    }
}
