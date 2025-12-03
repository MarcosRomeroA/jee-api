<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemovePlayerFromRoster;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class RemovePlayerFromRosterCommand implements Command
{
    public function __construct(
        public string $rosterId,
        public string $teamId,
        public string $playerId,
        public string $requesterId,
    ) {
    }
}
