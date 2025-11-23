<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TournamentRequestRepository
{
    public function save(TournamentRequest $request): void;

    public function findById(Uuid $id): ?TournamentRequest;

    public function findPendingByTournamentAndTeam(
        Uuid $tournamentId,
        Uuid $teamId,
    ): ?TournamentRequest;

    /**
     * @return TournamentRequest[]
     */
    public function findPendingByTournament(Uuid $tournamentId): array;

    /**
     * @return TournamentRequest[]
     */
    public function findAllPending(): array;
}
