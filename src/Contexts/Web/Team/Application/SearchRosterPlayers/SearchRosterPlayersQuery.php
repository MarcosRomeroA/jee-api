<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosterPlayers;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class SearchRosterPlayersQuery implements Query
{
    public function __construct(
        public string $rosterId,
        public string $teamId,
    ) {
    }
}
