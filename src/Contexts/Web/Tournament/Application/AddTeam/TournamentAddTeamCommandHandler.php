<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AddTeam;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TournamentAddTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TournamentTeamAdder $adder
    ) {
    }

    public function __invoke(TournamentAddTeamCommand $command): void
    {
        $this->adder->add(
            new Uuid($command->tournamentId),
            new Uuid($command->teamId),
            new Uuid($command->addedByUserId)
        );
    }
}

