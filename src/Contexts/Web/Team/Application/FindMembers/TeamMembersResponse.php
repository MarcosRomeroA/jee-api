<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindMembers;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class TeamMembersResponse extends Response
{
    /**
     * @param array<TeamMemberResponse> $members
     */
    public function __construct(
        public readonly array $members,
    ) {
    }

    public function toArray(): array
    {
        return array_map(
            fn (TeamMemberResponse $member) => $member->toArray(),
            $this->members
        );
    }
}
