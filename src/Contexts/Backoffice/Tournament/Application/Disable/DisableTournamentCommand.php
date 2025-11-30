<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DisableTournamentCommand implements Command
{
    public function __construct(
        public string $tournamentId,
        public string $reason,
    ) {
    }
}
