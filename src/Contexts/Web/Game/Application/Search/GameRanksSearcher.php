<?php

declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Search;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Application\Shared\GameRankCollectionResponse;
use App\Contexts\Web\Game\Application\Shared\GameRankResponse;
use App\Contexts\Web\Game\Domain\GameRankRepository;

final readonly class GameRanksSearcher
{
    public function __construct(
        private GameRankRepository $repository
    ) {
    }

    public function __invoke(Uuid $gameId): GameRankCollectionResponse
    {
        $gameRanks = $this->repository->findByGame($gameId);

        $gameRanksResponse = array_map(
            static fn ($gameRank) => GameRankResponse::fromGameRank($gameRank),
            $gameRanks
        );

        return new GameRankCollectionResponse($gameRanksResponse);
    }
}
