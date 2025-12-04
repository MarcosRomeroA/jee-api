<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Event\Application\Shared\EventCollectionResponse;
use App\Contexts\Web\Event\Domain\EventType;

final readonly class SearchEventsQueryHandler implements QueryHandler
{
    public function __construct(
        private EventsSearcher $searcher,
    ) {
    }

    public function __invoke(SearchEventsQuery $query): EventCollectionResponse
    {
        $gameId = $query->gameId !== null ? new Uuid($query->gameId) : null;
        $type = $query->type !== null ? EventType::tryFrom($query->type) : null;

        return $this->searcher->__invoke(
            $gameId,
            $type,
            $query->limit,
            $query->offset,
        );
    }
}
