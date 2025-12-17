<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FinalizeActiveTournaments;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class FinalizeActiveTournamentsCommandHandler implements CommandHandler
{
    public function __construct(
        private ActiveTournamentsFinalizer $finalizer,
    ) {
    }

    public function __invoke(FinalizeActiveTournamentsCommand $command): void
    {
        $this->finalizer->__invoke();
    }
}
