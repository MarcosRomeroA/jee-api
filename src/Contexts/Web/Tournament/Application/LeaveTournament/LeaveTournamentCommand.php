<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\LeaveTournament;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class LeaveTournamentCommand implements Command
{
    public function __construct(
        public string $tournamentId,
        public string $teamId,
        public string $userId,
    ) {
    }
}
