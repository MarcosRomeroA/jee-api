<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Search;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Shared\EventCollectionResponse;
use App\Contexts\Web\Event\Application\Shared\EventResponse;
use App\Contexts\Web\Event\Domain\Event;
use App\Contexts\Web\Event\Domain\EventRepository;
use App\Contexts\Web\Event\Domain\EventType;

final readonly class EventsSearcher
{
    public function __construct(
        private EventRepository $eventRepository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(
        ?Uuid $gameId,
        ?EventType $type,
        int $limit,
        int $offset,
    ): EventCollectionResponse {
        $events = $this->eventRepository->searchUpcoming(
            $gameId,
            $type,
            $limit,
            $offset,
        );

        $total = $this->eventRepository->countUpcoming($gameId, $type);

        $responses = array_map(
            fn (Event $event) => EventResponse::fromEvent($event, $this->cdnBaseUrl),
            $events
        );

        return new EventCollectionResponse(
            $responses,
            $total,
            $limit,
            $offset,
        );
    }
}
