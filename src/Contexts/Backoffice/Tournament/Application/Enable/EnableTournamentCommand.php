<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class EnableTournamentCommand implements Command
{
    public function __construct(
        public string $tournamentId,
    ) {
    }
}
