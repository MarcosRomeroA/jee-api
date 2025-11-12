<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchMyTournaments;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchMyTournamentsQuery implements Query
{
    public function __construct(
        public string $responsibleId,
        public ?string $query = null,
        public ?string $gameId = null
    ) {
    }
}

