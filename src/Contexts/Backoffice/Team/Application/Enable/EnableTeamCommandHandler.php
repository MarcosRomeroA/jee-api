<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class EnableTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamEnabler $enabler,
    ) {
    }

    public function __invoke(EnableTeamCommand $command): void
    {
        $this->enabler->__invoke(new Uuid($command->teamId));
    }
}
