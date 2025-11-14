<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchTournamentsQuery implements Query
{
    public function __construct(
        public ?string $query = null,
        public ?string $gameId = null,
        public ?string $responsibleId = null,
        public bool $open = false,
        public int $limit = 20,
        public int $offset = 0
    ) {
    }
}

