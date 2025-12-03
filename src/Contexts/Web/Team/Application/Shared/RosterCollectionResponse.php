<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class RosterCollectionResponse extends Response
{
    /**
     * @param RosterResponse[] $rosters
     */
    public function __construct(
        public readonly array $rosters,
        public readonly int $total = 0,
        public readonly int $limit = 20,
        public readonly int $offset = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            static fn (RosterResponse $roster) => $roster->toArray(),
            $this->rosters
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->rosters),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ]
        ];
    }
}
