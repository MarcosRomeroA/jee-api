<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class UpdateTournamentCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TournamentUpdater $updater
    ) {
    }

    public function __invoke(UpdateTournamentCommand $command): void
    {
        $this->updater->update(
            new Uuid($command->id),
            $command->name,
            $command->description,
            $command->maxTeams,
            $command->isOfficial,
            $command->image,
            $command->prize,
            $command->region,
            new \DateTimeImmutable($command->startAt),
            new \DateTimeImmutable($command->endAt)
        );
    }
}

