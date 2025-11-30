<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TeamCollectionResponse extends Response
{
    /**
     * @param TeamResponse[] $teams
     */
    public function __construct(
        private readonly array $teams,
        private readonly int $total,
        private readonly int $limit,
        private readonly int $offset,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (TeamResponse $team) => $team->toArray(), $this->teams),
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->teams),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
