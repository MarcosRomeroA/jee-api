<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Enable;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final class EnableUserCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {
    }
}
