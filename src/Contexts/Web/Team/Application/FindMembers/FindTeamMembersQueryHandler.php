<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindMembers;

use App\Contexts\Shared\Domain\CQRS\Query\QueryHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class FindTeamMembersQueryHandler implements QueryHandler
{
    public function __construct(
        private TeamMembersFinder $finder,
    ) {
    }

    public function __invoke(FindTeamMembersQuery $query): TeamMembersResponse
    {
        return $this->finder->__invoke(new Uuid($query->teamId));
    }
}
