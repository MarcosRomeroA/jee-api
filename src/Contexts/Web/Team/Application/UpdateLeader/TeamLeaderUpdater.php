<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\UpdateLeader;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final class TeamLeaderUpdater
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function update(
        Uuid $teamId,
        Uuid $newLeaderId,
        Uuid $requesterId
    ): void {
        $team = $this->teamRepository->findById($teamId);

        // Verificar que el solicitante es el creador del equipo
        if (!$team->isOwner($requesterId)) {
            throw new UnauthorizedException('Only the team creator can assign a new leader');
        }

        // Obtener el nuevo líder
        $newLeader = $this->userRepository->findById($newLeaderId);

        // Asignar el nuevo líder
        $team->setLeader($newLeader);

        $this->teamRepository->save($team);
    }
}
