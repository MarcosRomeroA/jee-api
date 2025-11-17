<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindTeams;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindUserTeamsQueryHandler implements QueryHandler
{
    public function __construct(
        private UserTeamsFinder $finder
    ) {
    }

    public function __invoke(FindUserTeamsQuery $query): array
    {
        $userId = new Uuid($query->userId);

        return ($this->finder)($userId);
    }
}
