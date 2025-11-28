<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\LeaveTeam;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\CannotLeaveAsOwnerException;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UserNotMemberException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\TeamUserRepository;

final readonly class TeamLeaver
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamUserRepository $teamUserRepository,
    ) {
    }

    public function __invoke(Uuid $teamId, Uuid $userId): void
    {
        $team = $this->teamRepository->findById($teamId);

        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }

        // El creador/owner no puede salirse del equipo
        if ($team->isOwner($userId)) {
            throw new CannotLeaveAsOwnerException($teamId->value());
        }

        $teamUser = $this->teamUserRepository->findByTeamAndUser($teamId, $userId);

        if ($teamUser === null) {
            throw new UserNotMemberException($teamId->value(), $userId->value());
        }

        $this->teamUserRepository->delete($teamUser);
    }
}
