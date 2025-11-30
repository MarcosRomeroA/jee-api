<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Team\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DisableTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamDisabler $disabler,
    ) {
    }

    public function __invoke(DisableTeamCommand $command): void
    {
        $teamId = new Uuid($command->teamId);
        $reason = ModerationReason::from($command->reason);

        $this->disabler->__invoke($teamId, $reason);
    }
}
