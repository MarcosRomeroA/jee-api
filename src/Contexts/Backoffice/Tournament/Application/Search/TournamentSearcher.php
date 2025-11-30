<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Search;

use App\Contexts\Backoffice\Tournament\Application\Shared\TournamentCollectionResponse;
use App\Contexts\Backoffice\Tournament\Application\Shared\TournamentResponse;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;

final readonly class TournamentSearcher
{
    public function __construct(
        private TournamentRepository $repository,
    ) {
    }

    public function __invoke(array $criteria): TournamentCollectionResponse
    {
        $tournaments = $this->repository->searchByCriteria($criteria);
        $total = $this->repository->countByCriteria($criteria);

        $responses = array_map(
            fn ($tournament) => TournamentResponse::fromEntity($tournament),
            $tournaments
        );

        return new TournamentCollectionResponse(
            tournaments: $responses,
            total: $total,
            limit: $criteria['limit'] ?? 20,
            offset: $criteria['offset'] ?? 0,
        );
    }
}
