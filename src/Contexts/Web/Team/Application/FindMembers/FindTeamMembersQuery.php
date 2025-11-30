<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindMembers;

use App\Contexts\Shared\Domain\CQRS\Query\Query;

final readonly class FindTeamMembersQuery implements Query
{
    public function __construct(
        public string $teamId,
    ) {
    }
}
