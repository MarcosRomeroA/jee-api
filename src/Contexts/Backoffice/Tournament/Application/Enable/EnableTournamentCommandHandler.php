<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class EnableTournamentCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentEnabler $enabler,
    ) {
    }

    public function __invoke(EnableTournamentCommand $command): void
    {
        $this->enabler->__invoke(new Uuid($command->tournamentId));
    }
}
