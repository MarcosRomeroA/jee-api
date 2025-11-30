<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class EnableTeamCommand implements Command
{
    public function __construct(
        public string $teamId,
    ) {
    }
}
