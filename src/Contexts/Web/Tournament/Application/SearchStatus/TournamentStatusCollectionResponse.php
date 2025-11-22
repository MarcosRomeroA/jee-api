<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchStatus;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TournamentStatusCollectionResponse extends Response
{
    /**
     * @param TournamentStatusResponse[] $statuses
     */
    public function __construct(
        public readonly array $statuses,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            static fn(TournamentStatusResponse $status) => $status->toArray(),
            $this->statuses,
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => count($data),
                'count' => count($data),
                'limit' => 0,
                'offset' => 0,
            ],
        ];
    }
}
