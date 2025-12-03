<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\AddPlayerToRoster;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class AddPlayerToRosterCommandHandler implements CommandHandler
{
    public function __construct(private RosterPlayerAdder $adder)
    {
    }

    public function __invoke(AddPlayerToRosterCommand $command): void
    {
        $this->adder->createOrUpdate(
            new Uuid($command->id),
            new Uuid($command->rosterId),
            new Uuid($command->teamId),
            new Uuid($command->playerId),
            $command->isStarter,
            $command->isLeader,
            $command->gameRoleId !== null ? new Uuid($command->gameRoleId) : null,
            new Uuid($command->requesterId),
        );
    }
}
