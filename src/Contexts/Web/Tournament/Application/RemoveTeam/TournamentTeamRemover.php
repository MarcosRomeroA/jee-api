<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\RemoveTeam;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidTournamentStateException;
use App\Contexts\Web\Tournament\Domain\Exception\TeamNotRegisteredException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final class TournamentTeamRemover
{
    public function __construct(
        private readonly TournamentRepository $tournamentRepository,
        private readonly TournamentTeamRepository $tournamentTeamRepository
    ) {
    }

    public function remove(Uuid $tournamentId, Uuid $teamId, Uuid $removedByUserId): void
    {
        // Verificar que existe el torneo
        $tournament = $this->tournamentRepository->findById($tournamentId);
        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        // Buscar la relación torneo-equipo
        $tournamentTeam = $this->tournamentTeamRepository->findByTournamentAndTeam($tournamentId, $teamId);
        if ($tournamentTeam === null) {
            throw new TeamNotRegisteredException('El equipo no está registrado en este torneo');
        }

        // Verificar permisos (responsable del torneo o creador del equipo)
        $isResponsible = $tournament->responsible()->id()->value() === $removedByUserId->value();
        $isCreator = $tournamentTeam->team()->creator() !== null && $tournamentTeam->team()->creator()->getId()->value() === $removedByUserId->value();

        if (!$isResponsible && !$isCreator) {
            throw new UnauthorizedException('No tiene permisos para eliminar este equipo del torneo');
        }

        // Verificar que el torneo no ha finalizado
        if ($tournament->status()->name() === 'finalized') {
            throw new InvalidTournamentStateException('No se pueden eliminar equipos de un torneo finalizado');
        }

        // Eliminar el equipo del torneo
        $tournament->decrementRegisteredTeams();

        $this->tournamentTeamRepository->delete($tournamentTeam);
        $this->tournamentRepository->save($tournament);
    }
}
