<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Disable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final class DisableUserCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {
    }
}
