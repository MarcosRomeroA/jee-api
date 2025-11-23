<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchTeamsQuery implements Query
{
    public function __construct(
        public ?string $query = null,
        public ?string $gameId = null,
        public ?string $creatorId = null,
        public ?string $userId = null,
        public ?string $tournamentId = null,
        public int $limit = 10,
        public int $offset = 0
    ) {
    }
}
