<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateTournamentBackgroundImageCommandHandler implements CommandHandler
{
    public function __construct(
        private TournamentBackgroundImageUpdater $updater,
    ) {
    }

    public function __invoke(UpdateTournamentBackgroundImageCommand $command): void
    {
        $this->updater->__invoke(
            new Uuid($command->tournamentId),
            new Uuid($command->requesterId),
            $command->image,
        );
    }
}
