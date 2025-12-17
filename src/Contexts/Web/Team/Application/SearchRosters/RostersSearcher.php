<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosters;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\RosterResponse;
use App\Contexts\Web\Team\Domain\RosterRepository;

final class RostersSearcher
{
    public function __construct(
        private readonly RosterRepository $rosterRepository,
        private readonly string $cdnBaseUrl,
    ) {
    }

    public function __invoke(Uuid $teamId, int $limit = 10, int $offset = 0): RosterCollectionResponse
    {
        $rosters = $this->rosterRepository->findByTeamIdWithPagination($teamId, $limit, $offset);
        $total = $this->rosterRepository->countByTeamId($teamId);

        $responses = array_map(
            fn ($roster) => RosterResponse::fromRoster($roster, $this->cdnBaseUrl),
            $rosters
        );

        return new RosterCollectionResponse($responses, $total);
    }
}
