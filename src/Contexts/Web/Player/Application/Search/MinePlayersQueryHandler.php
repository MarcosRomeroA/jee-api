<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Pagination;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class MinePlayersQueryHandler implements QueryHandler
{
    public function __construct(private readonly MinePlayersSearcher $searcher) {}

    public function __invoke(MinePlayersQuery $query): PaginatedResponse
    {
        $pagination = Pagination::fromRequest($query->page, $query->limit);
        return $this->searcher->search(
            $query->query,
            new Uuid($query->userId),
            $pagination
        );
    }
}

