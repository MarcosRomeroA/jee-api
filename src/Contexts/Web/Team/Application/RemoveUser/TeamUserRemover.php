<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemoveUser;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\CannotRemoveCreatorException;
use App\Contexts\Web\Team\Domain\Exception\CannotRemoveSelfException;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\Exception\UserNotMemberException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\TeamUserRepository;

final readonly class TeamUserRemover
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamUserRepository $teamUserRepository,
    ) {
    }

    public function __invoke(Uuid $teamId, Uuid $userIdToRemove, Uuid $requesterId): void
    {
        $team = $this->teamRepository->findById($teamId);

        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }

        // Solo el creador o líder puede expulsar miembros
        if (!$team->canEdit($requesterId)) {
            throw new UnauthorizedException("You don't have permission to remove members from this team.");
        }

        // No se puede expulsar a uno mismo
        if ($userIdToRemove->value() === $requesterId->value()) {
            throw new CannotRemoveSelfException();
        }

        // No se puede expulsar al creador
        if ($team->isOwner($userIdToRemove)) {
            throw new CannotRemoveCreatorException($teamId->value());
        }

        // Un líder no puede expulsar al creador (ya cubierto arriba)
        // Pero también validamos que un líder no puede expulsar a otro líder si no es el creador
        if ($team->isLeader($userIdToRemove) && !$team->isOwner($requesterId)) {
            throw new UnauthorizedException("Only the team creator can remove the leader.");
        }

        $teamUser = $this->teamUserRepository->findByTeamAndUser($teamId, $userIdToRemove);

        if ($teamUser === null) {
            throw new UserNotMemberException($teamId->value(), $userIdToRemove->value());
        }

        $this->teamUserRepository->delete($teamUser);
    }
}
