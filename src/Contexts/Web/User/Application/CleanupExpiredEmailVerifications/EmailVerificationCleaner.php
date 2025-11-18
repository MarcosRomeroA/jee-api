<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\CleanupExpiredEmailVerifications;

use Doctrine\DBAL\Connection;

final readonly class EmailVerificationCleaner
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(): int
    {
        // Delete email confirmations that expired more than 24 hours ago
        $deletedCount = $this->connection->executeStatement(
            'DELETE FROM email_confirmation WHERE expires_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)'
        );

        return $deletedCount;
    }
}
