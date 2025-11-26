<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class DisableUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserDisabler $disabler,
    ) {
    }

    public function __invoke(DisableUserCommand $command): void
    {
        $userId = new Uuid($command->userId);

        $this->disabler->__invoke($userId);
    }
}
