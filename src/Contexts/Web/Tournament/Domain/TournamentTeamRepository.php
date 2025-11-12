<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

interface TournamentTeamRepository
{
    public function save(TournamentTeam $tournamentTeam): void;
    public function findById(Uuid $id): ?TournamentTeam;
    public function findByTournamentAndTeam(Uuid $tournamentId, Uuid $teamId): ?TournamentTeam;
    public function findByTournament(Uuid $tournamentId): array;
    public function delete(TournamentTeam $tournamentTeam): void;
}

