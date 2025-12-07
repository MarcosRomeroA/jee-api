<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindMembers;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamUserRepository;

final readonly class TeamMembersFinder
{
    public function __construct(
        private TeamUserRepository $teamUserRepository,
        private string $cdnBaseUrl,
    ) {
    }

    public function __invoke(Uuid $teamId): TeamMembersResponse
    {
        $teamUsers = $this->teamUserRepository->findByTeam($teamId);

        $members = array_map(
            fn ($teamUser) => TeamMemberResponse::fromTeamUser($teamUser, $this->cdnBaseUrl),
            $teamUsers
        );

        return new TeamMembersResponse($members);
    }
}
