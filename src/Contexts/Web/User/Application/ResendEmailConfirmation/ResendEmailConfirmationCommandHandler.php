<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ResendEmailConfirmation;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class ResendEmailConfirmationCommandHandler implements CommandHandler
{
    public function __construct(
        private EmailConfirmationResender $resender
    ) {
    }

    public function __invoke(ResendEmailConfirmationCommand $command): void
    {
        $userId = new Uuid($command->userId());

        ($this->resender)($userId);
    }
}
