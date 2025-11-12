<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\AssignResponsible;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class TournamentAssignResponsibleCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TournamentResponsibleAssigner $assigner
    ) {
    }

    public function __invoke(TournamentAssignResponsibleCommand $command): void
    {
        $this->assigner->assign(
            new Uuid($command->tournamentId),
            new Uuid($command->newResponsibleId),
            new Uuid($command->currentResponsibleId)
        );
    }
}

