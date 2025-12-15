<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TournamentCollectionResponse extends Response
{
    /**
     * @param TournamentResponse[] $tournaments
     */
    public function __construct(
        public readonly array $tournaments,
        private readonly int $total = 0,
        private readonly int $limit = 10,
        private readonly int $offset = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            static fn (TournamentResponse $tournament) => $tournament->toArray(),
            $this->tournaments
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total ?: count($this->tournaments),
                'count' => count($this->tournaments),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ]
        ];
    }
}
