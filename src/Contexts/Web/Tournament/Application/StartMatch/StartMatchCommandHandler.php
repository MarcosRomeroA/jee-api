<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\StartMatch;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class StartMatchCommandHandler implements CommandHandler
{
    public function __construct(
        private MatchStarter $matchStarter
    ) {
    }

    public function __invoke(StartMatchCommand $command): void
    {
        $this->matchStarter->start(new Uuid($command->matchId));
    }
}
