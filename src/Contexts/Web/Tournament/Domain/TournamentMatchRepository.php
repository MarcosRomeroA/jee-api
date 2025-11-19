<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TournamentMatchRepository
{
    public function save(TournamentMatch $match): void;

    public function findById(Uuid $id): ?TournamentMatch;

    /**
     * @param Uuid $tournamentId
     * @return array<TournamentMatch>
     */
    public function findByTournamentId(Uuid $tournamentId): array;

    /**
     * @param Uuid $tournamentId
     * @param int $round
     * @return array<TournamentMatch>
     */
    public function findByTournamentIdAndRound(Uuid $tournamentId, int $round): array;

    public function delete(TournamentMatch $match): void;
}
