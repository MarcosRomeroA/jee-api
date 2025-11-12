<?php declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\Create;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final class CreateTeamCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly TeamCreator $creator
    ) {
    }

    public function __invoke(CreateTeamCommand $command): void
    {
        $this->creator->create(
            new Uuid($command->id),
            new Uuid($command->gameId),
            new Uuid($command->ownerId),
            $command->name,
            $command->image
        );
    }
}

