<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ConfirmEmail;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class ConfirmEmailCommandHandler implements CommandHandler
{
    public function __construct(
        private EmailConfirmer $emailConfirmer
    ) {
    }

    public function __invoke(ConfirmEmailCommand $command): void
    {
        $this->emailConfirmer->confirm($command->token);
    }
}
