<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class RosterPlayersCollectionResponse extends Response
{
    /**
     * @param RosterPlayerResponse[] $players
     */
    public function __construct(
        public readonly array $players,
        public readonly int $total = 0,
        public readonly int $limit = 20,
        public readonly int $offset = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            static fn (RosterPlayerResponse $player) => $player->toArray(),
            $this->players
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->players),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ]
        ];
    }
}
