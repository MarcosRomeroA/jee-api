<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\UpdateMatchResult;

use App\Contexts\Shared\Domain\Bus\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateMatchResultCommandHandler implements CommandHandler
{
    public function __construct(
        private MatchResultUpdater $matchResultUpdater
    ) {
    }

    public function __invoke(UpdateMatchResultCommand $command): void
    {
        $this->matchResultUpdater->update(
            new Uuid($command->matchId),
            $command->scores,
            $command->winnerId
        );
    }
}
