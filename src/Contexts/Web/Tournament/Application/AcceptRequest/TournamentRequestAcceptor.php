<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AcceptRequest;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidTournamentStateException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentFullException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentRequestNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentRequestRepository;
use App\Contexts\Web\Tournament\Domain\TournamentTeam;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final readonly class TournamentRequestAcceptor
{
    public function __construct(
        private TournamentRequestRepository $requestRepository,
        private TournamentTeamRepository $tournamentTeamRepository,
        private TournamentRepository $tournamentRepository,
    ) {
    }

    public function __invoke(Uuid $requestId, Uuid $acceptedByUserId): void
    {
        $request = $this->requestRepository->findById($requestId);

        if ($request === null || !$request->isPending()) {
            throw new TournamentRequestNotFoundException($requestId->value());
        }

        $tournament = $request->tournament();

        // Verificar que quien acepta es el responsable del torneo
        if ($tournament->responsible()->getId()->value() !== $acceptedByUserId->value()) {
            throw new UnauthorizedException(
                'Solo el responsable del torneo puede aceptar solicitudes'
            );
        }

        // Verificar que el torneo está activo o creado
        $status = $tournament->status();
        if (!$status->isCreated() && !$status->isActive()) {
            throw new InvalidTournamentStateException(
                'El torneo no acepta equipos en su estado actual'
            );
        }

        // Verificar que el torneo no está lleno
        if ($tournament->registeredTeams() >= $tournament->maxTeams()) {
            throw new TournamentFullException(
                'El torneo ya alcanzó el máximo de equipos'
            );
        }

        // Verificar fechas del torneo
        $now = new \DateTimeImmutable();
        if ($tournament->startAt() < $now) {
            throw new InvalidTournamentStateException(
                'El torneo ya ha comenzado'
            );
        }

        // Aceptar la solicitud
        $request->accept();
        $this->requestRepository->save($request);

        // Agregar el equipo al torneo
        $tournamentTeam = new TournamentTeam(
            Uuid::random(),
            $tournament,
            $request->team()
        );

        $tournament->incrementRegisteredTeams();

        $this->tournamentTeamRepository->save($tournamentTeam);
        $this->tournamentRepository->save($tournament);
    }
}
