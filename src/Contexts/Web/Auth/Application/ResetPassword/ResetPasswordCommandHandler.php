<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ResetPassword;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class ResetPasswordCommandHandler implements CommandHandler
{
    public function __construct(
        private PasswordResetter $resetter,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): void
    {
        $this->resetter->__invoke(
            $command->token,
            $command->password,
            $command->passwordConfirmation,
        );
    }
}
