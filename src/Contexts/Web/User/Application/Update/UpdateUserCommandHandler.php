<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserUpdater $userUpdater,
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $this->userUpdater->__invoke(
            new Uuid($command->id),
            $command->firstname,
            $command->lastname,
            $command->username,
            $command->email,
        );
    }
}
