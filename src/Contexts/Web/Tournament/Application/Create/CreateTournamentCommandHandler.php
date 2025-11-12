<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class CreateTournamentCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TournamentCreator $creator
    ) {
    }

    public function __invoke(CreateTournamentCommand $command): void
    {
        $this->creator->create(
            new Uuid($command->id),
            new Uuid($command->gameId),
            new Uuid($command->responsibleId),
            $command->name,
            $command->description,
            $command->maxTeams,
            $command->isOfficial,
            $command->image,
            $command->prize,
            $command->region,
            new \DateTimeImmutable($command->startAt),
            new \DateTimeImmutable($command->endAt),
            $command->minGameRankId ? new Uuid($command->minGameRankId) : null,
            $command->maxGameRankId ? new Uuid($command->maxGameRankId) : null
        );
    }
}

