<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SearchUserWonTournaments;

use App\Contexts\Shared\Domain\Response\PaginatedResponse;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Application\Shared\TournamentResponse;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class UserWonTournamentsSearcher
{
    public function __construct(
        private TournamentRepository $repository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(
        Uuid $userId,
        int $limit,
        int $page,
    ): PaginatedResponse {
        $offset = ($page - 1) * $limit;

        $tournaments = $this->repository->findWonByUserId($userId, $limit, $offset);
        $total = $this->repository->countWonByUserId($userId);

        $tournamentsResponse = array_map(
            fn ($tournament) => TournamentResponse::fromTournament($tournament, $this->cdnBaseUrl)->toArray(),
            $tournaments
        );

        return PaginatedResponse::create(
            $tournamentsResponse,
            $page,
            $total,
            $limit
        );
    }
}
