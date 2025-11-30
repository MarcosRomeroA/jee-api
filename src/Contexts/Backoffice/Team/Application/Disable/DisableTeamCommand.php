<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class DisableTeamCommand implements Command
{
    public function __construct(
        public string $teamId,
        public string $reason,
    ) {
    }
}
