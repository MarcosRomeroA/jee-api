<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\LeaveTournament;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Exception\TeamNotRegisteredException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final readonly class TournamentLeaver
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private TeamRepository $teamRepository,
        private TournamentTeamRepository $tournamentTeamRepository,
    ) {
    }

    public function __invoke(Uuid $tournamentId, Uuid $teamId, Uuid $userId): void
    {
        $tournament = $this->tournamentRepository->findById($tournamentId);

        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        $team = $this->teamRepository->findById($teamId);

        if ($team === null) {
            throw new TeamNotFoundException($teamId->value());
        }

        // Verificar que el usuario es el creador o líder del equipo
        if (!$team->isOwner($userId) && !$team->isLeader($userId)) {
            throw new UnauthorizedException(
                'Solo el creador o líder del equipo puede retirar al equipo del torneo'
            );
        }

        $tournamentTeam = $this->tournamentTeamRepository->findByTournamentAndTeam(
            $tournamentId,
            $teamId
        );

        if ($tournamentTeam === null) {
            throw new TeamNotRegisteredException(
                'El equipo no está registrado en este torneo'
            );
        }

        $tournament->decrementRegisteredTeams();

        $this->tournamentTeamRepository->delete($tournamentTeam);
        $this->tournamentRepository->save($tournament);
    }
}
