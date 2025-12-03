<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\CreateRoster;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class CreateRosterCommand implements Command
{
    public function __construct(
        public string $id,
        public string $teamId,
        public string $gameId,
        public string $name,
        public ?string $description = null,
        public ?string $logo = null,
        public string $requesterId = '',
    ) {
    }
}
