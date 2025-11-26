<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\User\Application\Search;

use App\Contexts\Backoffice\User\Application\Shared\UserCollectionResponse;
use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;

final readonly class SearchUsersQueryHandler implements QueryHandler
{
    public function __construct(
        private UserSearcher $searcher
    ) {
    }

    public function __invoke(SearchUsersQuery $query): UserCollectionResponse
    {
        return $this->searcher->__invoke($query->criteria);
    }
}
