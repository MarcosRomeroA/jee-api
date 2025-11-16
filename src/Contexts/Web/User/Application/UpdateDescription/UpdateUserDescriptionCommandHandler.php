<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateDescription;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateUserDescriptionCommandHandler implements CommandHandler
{
    public function __construct(
        private UserDescriptionUpdater $userDescriptionUpdater,
    ) {
    }

    public function __invoke(UpdateUserDescriptionCommand $command): void
    {
        $this->userDescriptionUpdater->__invoke(
            new Uuid($command->id),
            $command->description,
        );
    }
}
