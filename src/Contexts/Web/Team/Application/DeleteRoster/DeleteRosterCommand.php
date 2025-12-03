<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\DeleteRoster;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DeleteRosterCommand implements Command
{
    public function __construct(
        public string $rosterId,
        public string $teamId,
        public string $requesterId,
    ) {
    }
}
