<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindPendingRequests;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Web\Team\Application\Shared\TeamRequestCollectionResponse;

final readonly class FindPendingTeamRequestsQueryHandler implements QueryHandler
{
    public function __construct(
        private PendingTeamRequestsFinder $finder
    ) {
    }

    public function __invoke(FindPendingTeamRequestsQuery $query): TeamRequestCollectionResponse
    {
        return ($this->finder)();
    }
}
