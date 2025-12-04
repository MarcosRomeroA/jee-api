<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class EventCollectionResponse extends Response
{
    /**
     * @param array<EventResponse> $events
     */
    public function __construct(
        public readonly array $events,
        public readonly int $total = 0,
        public readonly int $limit = 10,
        public readonly int $offset = 0,
    ) {
    }

    public function toArray(): array
    {
        $data = array_map(
            static fn (EventResponse $event) => $event->toArray(),
            $this->events
        );

        return [
            'data' => $data,
            'metadata' => [
                'total' => $this->total,
                'count' => count($this->events),
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        ];
    }
}
