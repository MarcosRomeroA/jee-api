<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Game\Application\Shared\GameCollectionResponse;
use App\Contexts\Web\Game\Application\Shared\GameResponse;

final class SearchGamesQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly GamesSearcher $searcher
    ) {
    }

    public function __invoke(SearchGamesQuery $query): GameCollectionResponse
    {
        $games = $this->searcher->search($query->query);

        $gamesResponse = array_map(
            static fn($game) => GameResponse::fromGame($game),
            $games
        );

        return new GameCollectionResponse($gamesResponse);
    }
}

