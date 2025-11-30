<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Find;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamResponse;

final readonly class FindTeamQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamFinder $finder,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(FindTeamQuery $query): TeamResponse
    {
        $team = $this->finder->find(new Uuid($query->id));

        return TeamResponse::fromTeam($team, $this->fileManager);
    }
}
