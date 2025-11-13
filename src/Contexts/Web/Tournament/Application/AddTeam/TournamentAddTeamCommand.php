<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AddTeam;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class TournamentAddTeamCommand implements Command
{
    public function __construct(
        public string $tournamentId,
        public string $teamId,
        public string $addedByUserId
    ) {
    }
}

