<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchUserWonTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchUserWonTournamentsQuery implements Query
{
    public function __construct(
        public string $userId,
        public int $limit = 10,
        public int $page = 1,
    ) {
    }
}
