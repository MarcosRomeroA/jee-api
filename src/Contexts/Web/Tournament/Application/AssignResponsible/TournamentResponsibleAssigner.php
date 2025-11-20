<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AssignResponsible;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\InvalidTournamentStateException;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\User\Domain\UserRepository;

final readonly class TournamentResponsibleAssigner
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private UserRepository       $userRepository
    ) {
    }

    public function assign(Uuid $tournamentId, Uuid $newResponsibleId, Uuid $currentResponsibleId): void
    {
        // Verificar que existe el torneo
        $tournament = $this->tournamentRepository->findById($tournamentId);
        if ($tournament === null) {
            throw new TournamentNotFoundException($tournamentId->value());
        }

        // Verificar permisos (solo el responsable actual puede cambiar)
        if ($tournament->responsible()->getId()->value() !== $currentResponsibleId->value()) {
            throw new UnauthorizedException('Solo el responsable actual puede cambiar el responsable del torneo');
        }

        // Verificar que el torneo está en estado created o active
        if (!$tournament->status()->isCreated() && !$tournament->status()->isActive()) {
            throw new InvalidTournamentStateException('No se puede cambiar el responsable en el estado actual del torneo');
        }

        // Verificar que existe el nuevo responsable
        $newResponsible = $this->userRepository->findById($newResponsibleId);

        // Asignar el nuevo responsable
        $tournament->assignResponsible($newResponsible);

        $this->tournamentRepository->save($tournament);
    }
}
