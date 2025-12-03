<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\DeleteRoster;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DeleteRosterCommandHandler implements CommandHandler
{
    public function __construct(private RosterDeleter $deleter)
    {
    }

    public function __invoke(DeleteRosterCommand $command): void
    {
        $this->deleter->__invoke(
            new Uuid($command->rosterId),
            new Uuid($command->teamId),
            new Uuid($command->requesterId),
        );
    }
}
