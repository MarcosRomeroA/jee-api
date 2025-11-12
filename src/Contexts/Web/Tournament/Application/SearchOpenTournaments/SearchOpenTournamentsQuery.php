<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchOpenTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchOpenTournamentsQuery implements Query
{
    public function __construct(
        public ?string $query = null,
        public ?string $gameId = null
    ) {
    }
}

