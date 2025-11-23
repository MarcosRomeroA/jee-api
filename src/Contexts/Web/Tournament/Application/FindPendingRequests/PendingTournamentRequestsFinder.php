<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FindPendingRequests;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentRequestCollectionResponse;
use App\Contexts\Web\Tournament\Application\Shared\TournamentRequestResponse;
use App\Contexts\Web\Tournament\Domain\TournamentRequestRepository;

final readonly class PendingTournamentRequestsFinder
{
    public function __construct(
        private TournamentRequestRepository $requestRepository,
    ) {
    }

    public function __invoke(?Uuid $tournamentId = null): TournamentRequestCollectionResponse
    {
        if ($tournamentId !== null) {
            $requests = $this->requestRepository->findPendingByTournament($tournamentId);
        } else {
            $requests = $this->requestRepository->findAllPending();
        }

        $responses = array_map(
            fn ($request) => TournamentRequestResponse::fromTournamentRequest($request),
            $requests
        );

        return new TournamentRequestCollectionResponse($responses);
    }
}
