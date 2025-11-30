<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TournamentCollectionResponse extends Response
{
    /**
     * @param array<TournamentResponse> $tournaments
     */
    public function __construct(
        private readonly array $tournaments,
        private readonly int $total,
        private readonly int $limit,
        private readonly int $offset,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(
                fn (TournamentResponse $tournament) => $tournament->toArray(),
                $this->tournaments
            ),
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->tournaments),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
