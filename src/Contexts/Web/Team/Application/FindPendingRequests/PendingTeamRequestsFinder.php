<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindPendingRequests;

use App\Contexts\Web\Team\Application\Shared\TeamRequestCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\TeamRequestResponse;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;

final readonly class PendingTeamRequestsFinder
{
    public function __construct(
        private TeamRequestRepository $repository
    ) {
    }

    public function __invoke(): TeamRequestCollectionResponse
    {
        $teamRequests = $this->repository->findAllPending();

        $requestsResponse = !empty($teamRequests)
            ? array_map(
                static fn ($request) => TeamRequestResponse::fromTeamRequest($request),
                $teamRequests
            )
            : [];

        return new TeamRequestCollectionResponse($requestsResponse);
    }
}
