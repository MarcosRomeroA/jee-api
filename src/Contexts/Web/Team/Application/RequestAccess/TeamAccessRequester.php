<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RequestAccess;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RequestAlreadyExistsException;
use App\Contexts\Web\Team\Domain\Exception\UserAlreadyMemberException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\TeamRequest;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final class TeamAccessRequester
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly UserRepository $userRepository,
        private readonly TeamRequestRepository $teamRequestRepository,
        private readonly EventBus $eventBus,
    ) {
    }

    public function request(Uuid $teamId, Uuid $userId): void
    {
        // Verificar que existe el equipo
        $team = $this->teamRepository->findById($teamId);

        // Verificar que existe el usuario
        $user = $this->userRepository->findById($userId);

        // Verificar que el usuario no es ya miembro del equipo
        if ($team->isMember($userId)) {
            throw new UserAlreadyMemberException();
        }

        // Verificar que no existe una solicitud pendiente
        $existingRequest = $this->teamRequestRepository->findPendingByTeamAndUser(
            $teamId,
            $userId,
        );
        if ($existingRequest !== null) {
            throw new RequestAlreadyExistsException(
                "Ya existe una solicitud pendiente para este equipo",
            );
        }

        // Crear la solicitud
        $request = TeamRequest::create(Uuid::random(), $team, $user);

        $this->teamRequestRepository->save($request);
        $this->eventBus->publish($request->pullDomainEvents());
    }
}
