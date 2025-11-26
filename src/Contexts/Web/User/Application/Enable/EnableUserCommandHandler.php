<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class EnableUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserEnabler $enabler,
    ) {
    }

    public function __invoke(EnableUserCommand $command): void
    {
        $userId = new Uuid($command->userId);

        $this->enabler->__invoke($userId);
    }
}
