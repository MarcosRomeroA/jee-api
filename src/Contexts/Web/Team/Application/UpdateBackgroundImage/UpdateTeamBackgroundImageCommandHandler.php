<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateTeamBackgroundImageCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamBackgroundImageUpdater $updater,
    ) {
    }

    public function __invoke(UpdateTeamBackgroundImageCommand $command): void
    {
        $this->updater->__invoke(
            new Uuid($command->teamId),
            new Uuid($command->requesterId),
            $command->image,
        );
    }
}
