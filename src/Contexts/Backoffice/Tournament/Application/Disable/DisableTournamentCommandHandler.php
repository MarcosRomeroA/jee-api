<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Tournament\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DisableTournamentCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentDisabler $disabler,
    ) {
    }

    public function __invoke(DisableTournamentCommand $command): void
    {
        $this->disabler->__invoke(
            new Uuid($command->tournamentId),
            ModerationReason::from($command->reason),
        );
    }
}
