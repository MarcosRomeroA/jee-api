<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemoveUser;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class RemoveUserCommandHandler implements CommandHandler
{
    public function __construct(
        private TeamUserRemover $remover,
    ) {
    }

    public function __invoke(RemoveUserCommand $command): void
    {
        $this->remover->__invoke(
            new Uuid($command->teamId),
            new Uuid($command->userIdToRemove),
            new Uuid($command->requesterId),
        );
    }
}
