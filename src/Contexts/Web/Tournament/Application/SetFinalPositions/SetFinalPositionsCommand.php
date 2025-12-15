<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SetFinalPositions;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class SetFinalPositionsCommand implements Command
{
    public function __construct(
        public string $tournamentId,
        public string $firstPlaceTeamId,
        public ?string $secondPlaceTeamId,
        public ?string $thirdPlaceTeamId,
        public string $userId,
    ) {
    }
}
