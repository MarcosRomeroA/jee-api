<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateUserBackgroundImageCommandHandler implements CommandHandler
{
    public function __construct(
        private UserBackgroundImageUpdater $updater,
    ) {
    }

    public function __invoke(UpdateUserBackgroundImageCommand $command): void
    {
        $this->updater->__invoke(
            new Uuid($command->userId),
            $command->image,
        );
    }
}
