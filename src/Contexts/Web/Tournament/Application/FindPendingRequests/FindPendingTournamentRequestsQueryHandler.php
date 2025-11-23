<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindPendingRequests;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentRequestCollectionResponse;

final readonly class FindPendingTournamentRequestsQueryHandler implements QueryHandler
{
    public function __construct(
        private PendingTournamentRequestsFinder $finder,
    ) {
    }

    public function __invoke(FindPendingTournamentRequestsQuery $query): TournamentRequestCollectionResponse
    {
        $tournamentId = $query->tournamentId ? new Uuid($query->tournamentId) : null;

        return $this->finder->__invoke($tournamentId);
    }
}
