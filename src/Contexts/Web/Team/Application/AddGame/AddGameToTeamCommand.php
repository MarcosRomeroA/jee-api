<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddGame;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class AddGameToTeamCommand implements Command
{
    public function __construct(
        public string $teamId,
        public string $gameId
    ) {
    }
}
