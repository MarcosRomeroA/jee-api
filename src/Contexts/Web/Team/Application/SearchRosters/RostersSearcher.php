<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\SearchRosters;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\RosterCollectionResponse;
use App\Contexts\Web\Team\Application\Shared\RosterResponse;
use App\Contexts\Web\Team\Domain\RosterRepository;

final class RostersSearcher
{
    public function __construct(
        private readonly RosterRepository $rosterRepository,
        private readonly FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $teamId): RosterCollectionResponse
    {
        $rosters = $this->rosterRepository->findByTeamId($teamId);

        $responses = array_map(
            fn ($roster) => RosterResponse::fromRoster($roster, $this->fileManager),
            $rosters
        );

        return new RosterCollectionResponse($responses, count($responses));
    }
}
