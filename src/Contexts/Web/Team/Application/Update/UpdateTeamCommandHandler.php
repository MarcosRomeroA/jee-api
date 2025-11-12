<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class UpdateTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TeamUpdater $updater
    ) {
    }

    public function __invoke(UpdateTeamCommand $command): void
    {
        $this->updater->update(
            new Uuid($command->id),
            $command->name,
            $command->image
        );
    }
}

