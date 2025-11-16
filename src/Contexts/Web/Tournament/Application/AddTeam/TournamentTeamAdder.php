<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AddTeam;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidTournamentStateException;
use App\Contexts\Web\Tournament\Domain\Exception\TeamAlreadyRegisteredException;
use App\Contexts\Web\Tournament\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentFullException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentTeam;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final class TournamentTeamAdder
{
    public function __construct(
        private readonly TournamentRepository $tournamentRepository,
        private readonly TeamRepository $teamRepository,
        private readonly TournamentTeamRepository $tournamentTeamRepository,
    ) {}

    public function add(
        Uuid $tournamentId,
        Uuid $teamId,
        Uuid $addedByUserId,
    ): void {
        // Verificar que existe el torneo
        $tournament = $this->tournamentRepository->findById($tournamentId);
        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        // Verificar que existe el equipo
        $team = $this->teamRepository->findById($teamId);
        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }

        // Verificar permisos (responsable del torneo o creador del equipo)
        $isResponsible =
            $tournament->responsible()->getId()->value() ===
            $addedByUserId->value();
        $isCreator =
            $team->creator() !== null &&
            $team->creator()->getId()->value() === $addedByUserId->value();

        if (!$isResponsible && !$isCreator) {
            throw new UnauthorizedException(
                "No tiene permisos para agregar este equipo al torneo",
            );
        }

        // Verificar que el torneo está activo o creado
        $validStatuses = ["created", "active"];
        if (!in_array($tournament->status()->name(), $validStatuses)) {
            throw new InvalidTournamentStateException(
                "El torneo no acepta equipos en su estado actual",
            );
        }

        // Verificar que el torneo no está lleno
        if ($tournament->registeredTeams() >= $tournament->maxTeams()) {
            throw new TournamentFullException(
                "El torneo ya alcanzó el máximo de equipos",
            );
        }

        // Verificar que el equipo no está ya registrado
        $existingTeam = $this->tournamentTeamRepository->findByTournamentAndTeam(
            $tournamentId,
            $teamId,
        );
        if ($existingTeam !== null) {
            throw new TeamAlreadyRegisteredException(
                "El equipo ya está registrado en este torneo",
            );
        }

        // Verificar fechas del torneo
        $now = new \DateTimeImmutable();
        if ($tournament->startAt() < $now) {
            throw new InvalidTournamentStateException(
                "El torneo ya ha comenzado",
            );
        }

        // Agregar el equipo al torneo
        $tournamentTeam = new TournamentTeam(
            Uuid::random(),
            $tournament,
            $team,
        );

        $tournament->incrementRegisteredTeams();

        $this->tournamentTeamRepository->save($tournamentTeam);
        $this->tournamentRepository->save($tournament);
    }
}
