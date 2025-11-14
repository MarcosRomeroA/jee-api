<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Search;

use App\Contexts\Shared\Domain\Bus\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRankRepository;

final readonly class SearchGameRanksQueryHandler implements QueryHandler
{
    public function __construct(private GameRankRepository $repository)
    {
    }

    public function __invoke(SearchGameRanksQuery $query): array
    {
        $gameId = new Uuid($query->gameId);
        return $this->repository->findByGame($gameId);
    }
}

