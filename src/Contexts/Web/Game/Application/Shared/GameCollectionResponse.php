<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class GameCollectionResponse extends Response
{
    /**
     * @param GameResponse[] $games
     */
    public function __construct(
        public readonly array $games
    ) {
    }

    public function toArray(): array
    {
        return array_map(
            static fn(GameResponse $game) => $game->toArray(),
            $this->games
        );
    }
}

