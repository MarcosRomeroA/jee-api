<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\CleanupExpiredEmailVerifications;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final class CleanupExpiredEmailVerificationsCommand implements Command
{
    // No parameters needed for this command
}
