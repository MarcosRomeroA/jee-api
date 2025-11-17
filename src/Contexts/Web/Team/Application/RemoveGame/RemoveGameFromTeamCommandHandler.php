<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemoveGame;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class RemoveGameFromTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamGameRemover $remover
    ) {
    }

    public function __invoke(RemoveGameFromTeamCommand $command): void
    {
        $teamId = new Uuid($command->teamId);
        $gameId = new Uuid($command->gameId);

        ($this->remover)($teamId, $gameId);
    }
}
