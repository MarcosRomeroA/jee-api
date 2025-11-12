<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\RemoveTeam;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TournamentRemoveTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TournamentTeamRemover $remover
    ) {
    }

    public function __invoke(TournamentRemoveTeamCommand $command): void
    {
        $this->remover->remove(
            new Uuid($command->tournamentId),
            new Uuid($command->teamId),
            new Uuid($command->removedByUserId)
        );
    }
}

