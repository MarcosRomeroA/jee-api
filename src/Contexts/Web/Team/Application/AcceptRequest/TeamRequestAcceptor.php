<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AcceptRequest;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RequestNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\TeamUserRepository;
use App\Contexts\Web\Team\Domain\TeamUser;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;

final readonly class TeamRequestAcceptor
{
    public function __construct(
        private TeamRequestRepository $teamRequestRepository,
        private TeamUserRepository $teamUserRepository
    ) {
    }

    public function accept(Uuid $requestId, Uuid $acceptedByUserId): void
    {
        // Buscar la solicitud
        $request = $this->teamRequestRepository->findById($requestId);
        if ($request === null) {
            throw new RequestNotFoundException($requestId->value());
        }

        // Verificar que quien acepta es el creador del equipo
        if ($request->team()->creator() === null || $request->team()->creator()->getId()->value() !== $acceptedByUserId->value()) {
            throw new UnauthorizedException('Solo el creador del equipo puede aceptar solicitudes');
        }

        // Verificar que la solicitud está pendiente
        if ($request->status() !== 'pending') {
            throw new UnauthorizedException('La solicitud ya fue procesada');
        }

        // Aceptar la solicitud
        $request->accept();

        // Agregar el usuario al equipo
        $teamUser = new TeamUser(
            Uuid::random(),
            $request->team(),
            $request->user()
        );

        $this->teamUserRepository->save($teamUser);
        $this->teamRequestRepository->save($request);
    }
}
