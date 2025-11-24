<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\LeaveTournament;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class LeaveTournamentCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentLeaver $leaver,
    ) {
    }

    public function __invoke(LeaveTournamentCommand $command): void
    {
        $this->leaver->__invoke(
            new Uuid($command->tournamentId),
            new Uuid($command->teamId),
            new Uuid($command->userId),
        );
    }
}
