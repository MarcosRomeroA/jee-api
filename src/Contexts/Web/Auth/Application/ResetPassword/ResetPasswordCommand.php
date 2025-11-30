<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ResetPassword;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class ResetPasswordCommand implements Command
{
    public function __construct(
        public string $token,
        public string $password,
        public string $passwordConfirmation,
    ) {
    }
}
