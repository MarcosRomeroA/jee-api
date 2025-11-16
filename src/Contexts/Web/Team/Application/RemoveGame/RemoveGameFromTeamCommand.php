<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemoveGame;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class RemoveGameFromTeamCommand implements Command
{
    public function __construct(
        public string $teamId,
        public string $gameId
    ) {
    }
}
