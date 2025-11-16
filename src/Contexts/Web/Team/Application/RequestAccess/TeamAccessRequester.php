<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RequestAccess;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\PlayerRepository;
use App\Contexts\Web\Team\Domain\Exception\RequestAlreadyExistsException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Team\Domain\TeamRequest;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;

final class TeamAccessRequester
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly PlayerRepository $playerRepository,
        private readonly TeamRequestRepository $teamRequestRepository,
    ) {}

    public function request(Uuid $teamId, Uuid $playerId): void
    {
        // Verificar que existe el equipo
        $team = $this->teamRepository->findById($teamId);

        // Verificar que existe el jugador
        $player = $this->playerRepository->findById($playerId);

        // Verificar que no existe una solicitud pendiente
        $existingRequest = $this->teamRequestRepository->findPendingByTeamAndPlayer(
            $teamId,
            $playerId,
        );
        if ($existingRequest !== null) {
            throw new RequestAlreadyExistsException(
                "Ya existe una solicitud pendiente para este equipo",
            );
        }

        // Crear la solicitud
        $request = new TeamRequest(Uuid::random(), $team, $player);

        $this->teamRequestRepository->save($request);
    }
}
