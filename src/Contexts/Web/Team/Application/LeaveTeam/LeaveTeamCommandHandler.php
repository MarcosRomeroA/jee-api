<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\LeaveTeam;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class LeaveTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamLeaver $leaver,
    ) {
    }

    public function __invoke(LeaveTeamCommand $command): void
    {
        $this->leaver->__invoke(
            new Uuid($command->teamId),
            new Uuid($command->userId),
        );
    }
}
