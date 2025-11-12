<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Search;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchPlayersQuery implements Query
{
    public function __construct(
        public ?string $query = null,
        public ?string $gameId = null,
        public ?int $page = null,
        public ?int $limit = null
    ) {
    }
}

