<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class GameRankCollectionResponse extends Response
{
    /**
     * @param GameRankResponse[] $gameRanks
     */
    public function __construct(public readonly array $gameRanks) {}

    public function toArray(): array
    {
        // Devolver directamente el array de rangos sin estructura de paginación
        return array_values(
            array_map(
                static fn(GameRankResponse $gameRank) => $gameRank->toArray(),
                $this->gameRanks,
            ),
        );
    }
}
