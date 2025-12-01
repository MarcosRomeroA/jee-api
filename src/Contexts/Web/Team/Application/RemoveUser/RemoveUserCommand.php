<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\RemoveUser;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class RemoveUserCommand implements Command
{
    public function __construct(
        public string $teamId,
        public string $userIdToRemove,
        public string $requesterId,
    ) {
    }
}
