<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\CreateMatch;

use App\Contexts\Shared\Domain\Bus\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class CreateMatchCommandHandler implements CommandHandler
{
    public function __construct(
        private MatchCreator $matchCreator
    ) {
    }

    public function __invoke(CreateMatchCommand $command): void
    {
        $this->matchCreator->create(
            new Uuid($command->id),
            new Uuid($command->tournamentId),
            $command->round,
            $command->teamIds,
            $command->name,
            $command->scheduledAt
        );
    }
}
