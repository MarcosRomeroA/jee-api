<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddPlayerToRoster;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class AddPlayerToRosterCommand implements Command
{
    public function __construct(
        public string $id,
        public string $rosterId,
        public string $teamId,
        public string $playerId,
        public bool $isStarter = false,
        public bool $isLeader = false,
        public ?string $gameRoleId = null,
        public string $requesterId = '',
    ) {
    }
}
