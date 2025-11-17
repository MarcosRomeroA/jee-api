<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddGame;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class AddGameToTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamGameAdder $adder
    ) {
    }

    public function __invoke(AddGameToTeamCommand $command): void
    {
        $teamId = new Uuid($command->teamId);
        $gameId = new Uuid($command->gameId);

        ($this->adder)($teamId, $gameId);
    }
}
