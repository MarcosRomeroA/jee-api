<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindPendingRequests;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindPendingTournamentRequestsQuery implements Query
{
    public function __construct(
        public ?string $tournamentId = null,
    ) {
    }
}
