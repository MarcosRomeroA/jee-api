<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TeamCollectionResponse extends Response
{
    /**
     * @param TeamResponse[] $teams
     */
    public function __construct(
        public readonly array $teams,
        public readonly int $total = 0,
        public readonly int $limit = 20,
        public readonly int $offset = 0
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            static fn(TeamResponse $team) => $team->toArray(),
            $this->teams
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->teams),
                'limit' => $this->limit,
                'offset' => $this->offset
            ]
        ];
    }
}

