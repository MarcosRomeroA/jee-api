<?php

declare(strict_types=1);

namespace App\Contexts\Web\Auth\Application\ForgotPassword;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class ForgotPasswordCommand implements Command
{
    public function __construct(
        public string $email,
    ) {
    }
}
