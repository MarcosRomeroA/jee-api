<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class CreateTournamentCommandHandler implements CommandHandler
{
    public function __construct(private readonly TournamentCreator $creator)
    {
    }

    public function __invoke(CreateTournamentCommand $command): void
    {
        $this->creator->create(
            new Uuid($command->id),
            new Uuid($command->gameId),
            $command->name,
            $command->isOfficial,
            new Uuid($command->responsibleId),
            $command->description,
            $command->rules,
            $command->maxTeams,
            $command->image,
            $command->prize,
            $command->region,
            $command->startAt
                ? new \DateTimeImmutable($command->startAt)
                : null,
            $command->endAt ? new \DateTimeImmutable($command->endAt) : null,
            $command->minGameRankId ? new Uuid($command->minGameRankId) : null,
            $command->maxGameRankId ? new Uuid($command->maxGameRankId) : null,
        );
    }
}
