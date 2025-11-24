<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchTournamentsQuery implements Query
{
    public function __construct(
        public ?string $name = null,
        public ?string $gameId = null,
        public ?string $statusId = null,
        public ?string $responsibleId = null,
        public bool $open = false,
        public int $limit = 10,
        public int $offset = 0,
        public ?string $currentUserId = null,
    ) {
    }
}
