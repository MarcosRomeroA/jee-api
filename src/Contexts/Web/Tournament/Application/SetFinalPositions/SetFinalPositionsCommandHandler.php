<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\SetFinalPositions;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class SetFinalPositionsCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentFinalPositionsSetter $setter,
    ) {
    }

    public function __invoke(SetFinalPositionsCommand $command): void
    {
        $this->setter->__invoke(
            new Uuid($command->tournamentId),
            new Uuid($command->firstPlaceTeamId),
            $command->secondPlaceTeamId !== null ? new Uuid($command->secondPlaceTeamId) : null,
            $command->thirdPlaceTeamId !== null ? new Uuid($command->thirdPlaceTeamId) : null,
            new Uuid($command->userId),
        );
    }
}
