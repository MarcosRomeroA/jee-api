<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ForgotPassword;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class ForgotPasswordCommandHandler implements CommandHandler
{
    public function __construct(
        private PasswordResetRequestor $requestor,
    ) {
    }

    public function __invoke(ForgotPasswordCommand $command): void
    {
        $this->requestor->__invoke($command->email);
    }
}
