<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemovePlayerFromRoster;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class RemovePlayerFromRosterCommandHandler implements CommandHandler
{
    public function __construct(private RosterPlayerRemover $remover)
    {
    }

    public function __invoke(RemovePlayerFromRosterCommand $command): void
    {
        $this->remover->__invoke(
            new Uuid($command->rosterId),
            new Uuid($command->teamId),
            new Uuid($command->playerId),
            new Uuid($command->requesterId),
        );
    }
}
