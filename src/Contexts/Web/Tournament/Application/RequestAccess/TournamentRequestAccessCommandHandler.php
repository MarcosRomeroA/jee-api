<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\RequestAccess;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class TournamentRequestAccessCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentAccessRequester $requester,
    ) {
    }

    public function __invoke(TournamentRequestAccessCommand $command): void
    {
        $this->requester->__invoke(
            new Uuid($command->tournamentId),
            new Uuid($command->teamId),
        );
    }
}
