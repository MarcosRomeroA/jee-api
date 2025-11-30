<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Search;

use App\Contexts\Backoffice\Team\Application\Shared\TeamCollectionResponse;
use App\Contexts\Backoffice\Team\Application\Shared\TeamResponse;
use App\Contexts\Web\Team\Domain\TeamRepository;

final readonly class TeamSearcher
{
    public function __construct(
        private TeamRepository $repository,
    ) {
    }

    public function __invoke(array $criteria): TeamCollectionResponse
    {
        $teams = $this->repository->searchByCriteria($criteria);
        $total = $this->repository->countByCriteria($criteria);

        $responses = array_map(
            fn ($team) => TeamResponse::fromEntity($team),
            $teams
        );

        return new TeamCollectionResponse(
            $responses,
            $total,
            $criteria['limit'] ?? 20,
            $criteria['offset'] ?? 0,
        );
    }
}
