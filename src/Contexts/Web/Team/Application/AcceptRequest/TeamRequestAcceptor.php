<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AcceptRequest;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RequestNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\TeamPlayerRepository;
use App\Contexts\Web\Team\Domain\TeamPlayer;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;

final readonly class TeamRequestAcceptor
{
    public function __construct(
        private TeamRequestRepository $teamRequestRepository,
        private TeamPlayerRepository  $teamPlayerRepository
    ) {
    }

    public function accept(Uuid $requestId, Uuid $acceptedByUserId): void
    {
        // Buscar la solicitud
        $request = $this->teamRequestRepository->findById($requestId);
        if ($request === null) {
            throw new RequestNotFoundException($requestId->value());
        }

        // Verificar que quien acepta es el dueño del equipo
        if ($request->team()->owner()->getId()->value() !== $acceptedByUserId->value()) {
            throw new UnauthorizedException('Solo el dueño del equipo puede aceptar solicitudes');
        }

        // Verificar que la solicitud está pendiente
        if ($request->status() !== 'pending') {
            throw new UnauthorizedException('La solicitud ya fue procesada');
        }

        // Aceptar la solicitud
        $request->accept();

        // Agregar el jugador al equipo
        $teamPlayer = new TeamPlayer(
            Uuid::random(),
            $request->team(),
            $request->player()
        );

        $this->teamPlayerRepository->save($teamPlayer);
        $this->teamRequestRepository->save($request);
    }
}

