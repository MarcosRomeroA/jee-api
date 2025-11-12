<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class UpdatePlayerCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly PlayerUpdater $updater
    ) {
    }

    public function __invoke(UpdatePlayerCommand $command): void
    {
        $this->updater->update(
            new Uuid($command->id),
            $command->username,
            new Uuid($command->gameRoleId),
            new Uuid($command->gameRankId)
        );
    }
}

