<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\ConfirmEmail;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class ConfirmEmailCommand implements Command
{
    public function __construct(
        public string $token
    ) {
    }
}
