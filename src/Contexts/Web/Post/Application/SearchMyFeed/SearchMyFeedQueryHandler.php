<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Application\SearchMyFeed;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Shared\PostCollectionResponse;
use Exception;

final readonly class SearchMyFeedQueryHandler implements QueryHandler
{
    public function __construct(
        private MyFeedSearcher $searcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SearchMyFeedQuery $query): PostCollectionResponse
    {
        $userId = new Uuid($query->id);

        return $this->searcher->__invoke($userId, $query->criteria);
    }
}