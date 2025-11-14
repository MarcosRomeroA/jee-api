<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class SearchPlayersQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly PlayersSearcher $searcher
    ) {
    }

    public function __invoke(SearchPlayersQuery $query): PaginatedResponse
    {
        $pagination = Pagination::fromRequest($query->page, $query->limit);

        return $this->searcher->search(
            $query->query,
            $query->gameId ? new Uuid($query->gameId) : null,
            $query->userId ? new Uuid($query->userId) : null,
            $pagination
        );
    }
}

