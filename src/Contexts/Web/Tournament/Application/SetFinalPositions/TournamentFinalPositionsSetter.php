<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SetFinalPositions;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\TeamNotFoundException as TeamDomainNotFoundException;
use App\Contexts\Web\Team\Domain\Team;
use App\Contexts\Web\Team\Domain\TeamRepository;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidTournamentStateException;
use App\Contexts\Web\Tournament\Domain\Exception\TeamNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\TeamNotRegisteredException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;
use App\Contexts\Web\Tournament\Domain\TournamentStatusRepository;
use App\Contexts\Web\Tournament\Domain\TournamentTeamRepository;

final readonly class TournamentFinalPositionsSetter
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private TournamentStatusRepository $tournamentStatusRepository,
        private TournamentTeamRepository $tournamentTeamRepository,
        private TeamRepository $teamRepository,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(
        Uuid $tournamentId,
        Uuid $firstPlaceTeamId,
        ?Uuid $secondPlaceTeamId,
        ?Uuid $thirdPlaceTeamId,
        Uuid $userId,
    ): void {
        $tournament = $this->tournamentRepository->findById($tournamentId);
        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        // Verify user is creator or responsible
        if (!$tournament->isResponsible($userId) && !$tournament->isCreator($userId)) {
            throw new UnauthorizedException(
                'Solo el creador o responsable del torneo puede definir las posiciones finales'
            );
        }

        // Verify tournament is in valid state (Created, Active or already Finalized for updates)
        $status = $tournament->getStatus();
        if (!$status->isCreated() && !$status->isActive() && !$status->isFinalized()) {
            throw new InvalidTournamentStateException(
                'El torneo debe estar creado o activo para definir las posiciones finales'
            );
        }

        // Verify first place team exists and is registered (required)
        $firstPlaceTeam = $this->getRegisteredTeam($firstPlaceTeamId, $tournamentId, 'primer');

        // Verify second and third place teams if provided (optional)
        $secondPlaceTeam = $secondPlaceTeamId !== null
            ? $this->getRegisteredTeam($secondPlaceTeamId, $tournamentId, 'segundo')
            : null;

        $thirdPlaceTeam = $thirdPlaceTeamId !== null
            ? $this->getRegisteredTeam($thirdPlaceTeamId, $tournamentId, 'tercer')
            : null;

        // Get finalized status
        $finalizedStatus = $this->tournamentStatusRepository->findById(
            new Uuid(TournamentStatus::FINALIZED)
        );

        // Set final positions and change status
        $tournament->setFinalPositions(
            $firstPlaceTeam,
            $secondPlaceTeam,
            $thirdPlaceTeam,
            $finalizedStatus,
        );

        $this->tournamentRepository->save($tournament);
        $this->eventBus->publish(...$tournament->pullDomainEvents());
    }

    private function getRegisteredTeam(Uuid $teamId, Uuid $tournamentId, string $position): Team
    {
        try {
            $team = $this->teamRepository->findById($teamId);
        } catch (TeamDomainNotFoundException) {
            throw new TeamNotFoundException(
                "El equipo para el $position puesto no fue encontrado"
            );
        }

        $tournamentTeam = $this->tournamentTeamRepository->findByTournamentAndTeam(
            $tournamentId,
            $teamId
        );

        if ($tournamentTeam === null) {
            throw new TeamNotRegisteredException(
                "El equipo '{$team->getName()}' no está registrado en el torneo"
            );
        }

        return $team;
    }
}
