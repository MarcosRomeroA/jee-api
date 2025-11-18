<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\CleanupExpiredEmailVerifications;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;

final readonly class CleanupExpiredEmailVerificationsCommandHandler implements CommandHandler
{
    public function __construct(
        private EmailVerificationCleaner $cleaner
    ) {
    }

    public function __invoke(CleanupExpiredEmailVerificationsCommand $command): int
    {
        return $this->cleaner->__invoke();
    }
}
