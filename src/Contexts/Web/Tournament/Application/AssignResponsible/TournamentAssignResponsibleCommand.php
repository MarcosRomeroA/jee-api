<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AssignResponsible;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class TournamentAssignResponsibleCommand implements Command
{
    public function __construct(
        public string $tournamentId,
        public string $newResponsibleId,
        public string $currentResponsibleId
    ) {
    }
}

