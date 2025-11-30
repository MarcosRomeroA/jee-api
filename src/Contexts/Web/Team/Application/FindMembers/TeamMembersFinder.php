<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\FindMembers;

use App\Contexts\Shared\Domain\FileManager\FileManager;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamUserRepository;

final readonly class TeamMembersFinder
{
    public function __construct(
        private TeamUserRepository $teamUserRepository,
        private FileManager $fileManager,
    ) {
    }

    public function __invoke(Uuid $teamId): TeamMembersResponse
    {
        $teamUsers = $this->teamUserRepository->findByTeam($teamId);

        $members = array_map(
            fn ($teamUser) => TeamMemberResponse::fromTeamUser(
                $teamUser,
                $this->fileManager->generateTemporaryUrl(
                    'user/profile',
                    $teamUser->getUser()->getProfileImage()->value()
                )
            ),
            $teamUsers
        );

        return new TeamMembersResponse($members);
    }
}
