<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindTeamGames;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Application\Shared\TeamGameCollectionResponse;

final readonly class FindTeamGamesQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamGamesFinder $finder
    ) {
    }

    public function __invoke(FindTeamGamesQuery $query): TeamGameCollectionResponse
    {
        $teamId = new Uuid($query->teamId);

        return ($this->finder)($teamId);
    }
}
