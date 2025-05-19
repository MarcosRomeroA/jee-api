<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\User\Application\Shared\UserCollectionResponse;
use Exception;

final readonly class SearchUsersQueryHandler implements QueryHandler
{
    public function __construct(
        private UserSearcher $searcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SearchUsersQuery $query): UserCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}