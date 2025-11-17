<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\FindAllByGame;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Search\GameRanksSearcher;
use App\Contexts\Web\Game\Application\Shared\GameRankCollectionResponse;

final readonly class FindAllGameRanksByGameQueryHandler implements QueryHandler
{
    public function __construct(
        private GameRanksSearcher $searcher
    ) {
    }

    public function __invoke(FindAllGameRanksByGameQuery $query): GameRankCollectionResponse
    {
        $gameId = new Uuid($query->gameId);

        return ($this->searcher)($gameId);
    }
}
