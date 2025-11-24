<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindPendingRequests;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindPendingTeamRequestsQuery implements Query
{
    public function __construct(
        public ?string $teamId = null,
    ) {
    }
}
