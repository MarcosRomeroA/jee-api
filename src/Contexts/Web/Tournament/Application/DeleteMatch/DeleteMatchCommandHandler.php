<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\DeleteMatch;

use App\Contexts\Shared\Domain\Bus\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DeleteMatchCommandHandler implements CommandHandler
{
    public function __construct(
        private MatchDeleter $matchDeleter
    ) {
    }

    public function __invoke(DeleteMatchCommand $command): void
    {
        $this->matchDeleter->delete(new Uuid($command->matchId));
    }
}
