<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\CreateRoster;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class CreateRosterCommandHandler implements CommandHandler
{
    public function __construct(private RosterCreator $creator)
    {
    }

    public function __invoke(CreateRosterCommand $command): void
    {
        $this->creator->createOrUpdate(
            new Uuid($command->id),
            new Uuid($command->teamId),
            new Uuid($command->gameId),
            $command->name,
            $command->description,
            $command->logo,
            new Uuid($command->requesterId),
        );
    }
}
