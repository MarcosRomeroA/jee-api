<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchUserWonTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class SearchUserWonTournamentsQueryHandler implements QueryHandler
{
    public function __construct(
        private UserWonTournamentsSearcher $searcher,
    ) {
    }

    public function __invoke(SearchUserWonTournamentsQuery $query): PaginatedResponse
    {
        return $this->searcher->__invoke(
            new Uuid($query->userId),
            $query->limit,
            $query->page,
        );
    }
}
