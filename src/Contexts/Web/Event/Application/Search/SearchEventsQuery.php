<?php

declare(strict_types=1);

namespace App\Contexts\Web\Event\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchEventsQuery implements Query
{
    public function __construct(
        public ?string $gameId = null,
        public ?string $type = null,
        public int $limit = 10,
        public int $offset = 0,
    ) {
    }
}
