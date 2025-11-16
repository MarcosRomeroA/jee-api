<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\UpdateLeader;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateTeamLeaderCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamLeaderUpdater $updater
    ) {}

    public function __invoke(UpdateTeamLeaderCommand $command): void
    {
        $this->updater->update(
            new Uuid($command->teamId),
            new Uuid($command->newLeaderId),
            new Uuid($command->requesterId)
        );
    }
}
