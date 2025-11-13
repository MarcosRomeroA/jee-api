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
        $data = array_map(
            static fn(GameResponse $game) => $game->toArray(),
            $this->games
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => count($this->games),
                'count' => count($this->games),
                'limit' => 0,
                'offset' => 0
            ]
        ];
    }
}
