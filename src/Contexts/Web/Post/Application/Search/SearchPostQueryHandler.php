<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;

final readonly class SearchPostQueryHandler implements QueryHandler
{
    public function __construct(
        private PostSearcher $searcher
    )
    {
    }

    public function __invoke(SearchPostQuery $query): PostCollectionResponse
    {
        $criteria = $query->criteria; // TODO: PostCriteria

        return $this->searcher->__invoke();
    }
}